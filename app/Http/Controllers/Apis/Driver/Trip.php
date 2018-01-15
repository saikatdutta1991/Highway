<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use App\Repositories\Email;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Repositories\SocketIOClient;
use App\Models\Trip as TripModel;
use App\Models\TripPoint;
use App\Repositories\Utill;
use App\Models\VehicleType;
use App\Models\RideFare;
use Validator;

class Trip extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(RideFare $rideFare, VehicleType $vehicleType, TripPoint $tripPoint, TripModel $trip, Utill $utill, Setting $setting, Email $email, Api $api, SocketIOClient $socketIOClient)
    {
        $this->rideFare = $rideFare;
        $this->vehicleType = $vehicleType;
        $this->tripPoint = $tripPoint;
        $this->trip = $trip;
        $this->utill = $utill;
        $this->setting = $setting;
        $this->email = $email;
        $this->api = $api;
        $this->socketIOClient = $socketIOClient;
    }


    /**
     * create trip for driver
     */
    public function createTrip(Request $request)
    {

        //validate trip create request       
        $validator = Validator::make(
            $request->all(), $this->trip->createTripValidationRules($request)
        );

        //if validation fails
        if($validator->fails()) {
            
            $errors = [];
            foreach($validator->errors()->getMessages() as $fieldName => $msgArr) {
                $errors[$fieldName] = $msgArr[0];
            }
            return $this->api->json(false, 'VALIDATION_ERROR', 'Fill all the fields before create trip', [
                'errors' => $errors
            ]);
        }

        $trip = new $this->trip;
        $trip->driver_id = $request->auth_driver->id;
        $trip->trip_name = ucfirst(trim($request->trip_name));
        $trip->seats = $request->seats;
        $trip->seats_available = $request->seats;
        $trip->source_address = trim($request->source_address);
        $trip->source_latitude = $request->source_latitude;
        $trip->source_longitude = $request->source_longitude;
        $trip->destination_address = trim($request->destination_address);
        $trip->destination_latitude = $request->destination_latitude;
        $trip->destination_longitude = $request->destination_longitude;
        $trip->trip_date_time = $this->utill->timestampStringToUTC($request->trip_date_time, $request->auth_driver->timezone)->toDateTimeString();
        $trip->trip_status = TripModel::INITIATED;

        try {

            DB::beginTransaction();
            
            $trip->save();


            /**
             * get ride fare for further ride calculation
             */
            $vTypeId = $this->vehicleType->getIdByCode($request->auth_driver->vehicle_type);
            $rideFare = $this->rideFare->where('vehicle_type_id', $vTypeId)->first();
        
            
            //creates first trip point with source and destination
            $tripPointParent = new $this->tripPoint;
            $tripPointParent->trip_id = $trip->id;
            $tripPointParent->trip_points_parent_id = 0;
            $tripPointParent->seats_booked = 0;
            $tripPointParent->source_address = trim($request->source_address);
            $tripPointParent->source_latitude = $request->source_latitude;
            $tripPointParent->source_longitude = $request->source_longitude;
            $tripPointParent->destination_address = trim($request->destination_address);
            $tripPointParent->destination_latitude = $request->destination_latitude;
            $tripPointParent->destination_longitude = $request->destination_longitude;
            $tripPointParent->estimated_trip_time = $request->estimated_trip_time;
            $tripPointParent->estimated_trip_distance = $request->estimated_trip_distance;

            $fare = $rideFare->calculateFare($request->estimated_trip_distance, $request->estimated_trip_time);
            $tripPointParent->estimated_fare = $fare['total'];

            $tripPointParent->trip_status = TripModel::INITIATED;
            $tripPointParent->save();

            //creates intermediate pickup points
            $tripPoints = [];
            $otherPickupPoints = is_array($request->other_pickup_points) ? $request->$request->other_pickup_points : [];
            foreach( $otherPickupPoints  as $index => $pickupPoint) {

                $fare = $rideFare->calculateFare($pickupPoint['estimated_trip_distance'], $pickupPoint['estimated_trip_time']);               

                $childTripPoint = [
                    'trip_id' => $trip->id,
                    'trip_points_parent_id' => $tripPointParent->id,
                    'seats_booked' => 0,
                    'source_address' => trim($pickupPoint['address']),
                    'source_latitude' => $pickupPoint['latitude'],
                    'source_longitude' => $pickupPoint['longitude'],
                    'destination_address' => trim($tripPointParent->destination_address),
                    'destination_latitude' => $tripPointParent->destination_latitude,
                    'destination_longitude' => $tripPointParent->destination_longitude,
                    'estimated_trip_time' => $pickupPoint['estimated_trip_time'],
                    'estimated_trip_distance' => $pickupPoint['estimated_trip_distance'],
                    'estimated_fare' => $fare['total'],
                    'trip_status' => TripModel::INITIATED,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];
                $tripPoints[] = $childTripPoint;
            }

            //if only other pickup points exists
            if(!empty($tripPoints)) {
                $this->tripPoint->insert($tripPoints);
            }
            
            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('CREATE_TRIP_ERROR', $e->getMessage());
            return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }
        
        
        $trip = $this->trip->with('pickupPoints')->find($trip->id);
        return $this->api->json(true, "TRIP_CREATED", 'Trip created', [
            'trip' => $trip,
        ]);
        

    }




    /**
     * delete a particular trip if only if trip status is initiated
     */
    public function deleteTrip(Request $request)
    {
        //fetching correct trip to delete
        $trip = $this->trip->where('id', $request->trip_id)
        ->where('driver_id', $request->auth_driver->id)
        ->where('trip_status', TripModel::INITIATED)
        ->first();

        //if no trip found means driver not allowed to delete
        if(!$trip) {
            return $this->api->json(false, "INVALID", 'You are not allowed to delete this trip any more. You can cancel it.');
        }


        try {

            DB::beginTransaction();
            
            $this->trip->deleteTrip($trip->id);
            
            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('DELETE_TRIP_ERROR', $e->getMessage());
            return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }

        return $this->api->json(false, 'TRIP_DELETED', 'Trip deleted');
        
    }




    /**
     * delete trip pickup point
     * if trip point is the parent trip point then not supposed to delete
     * rather ask driver to delete whole trip
     */
    public function deleteTripPickupPoint(Request $request)
    {
        //fetching correct trippoint to delete
        $tripTable = $this->trip->getTableName();
        $tripPoint = $this->tripPoint->join($tripTable, "{$tripTable}.id", '=', "{$this->tripPoint->getTableName()}.trip_id")
        ->where("{$tripTable}.id", $request->trip_id)
        ->where("{$tripTable}.driver_id", $request->auth_driver->id)
        ->where("{$this->tripPoint->getTableName()}.trip_points_parent_id", '<>', 0)
        ->where("{$this->tripPoint->getTableName()}.id", '=', $request->pickup_point_id)
        ->where("{$this->tripPoint->getTableName()}.trip_status", TripModel::INITIATED)
        ->select("{$this->tripPoint->getTableName()}.*")
        ->first();

        if(!$tripPoint) { 
            return $this->api->json(false, "INVALID", 'You are not allowed to delete this trip point.');
        }

        //fetching corrent trip point
        $tripPoint->forceDelete();

        return $this->api->json(false, 'TRIP_POINT_DELETED', 'Trip point deleted');


    }




    /**
     * add new pickup point
     */
    public function addPickupPoint(Request $request)
    {

        //validate trip create request       
        $validator = Validator::make(
            $request->all(), $this->tripPoint->keyRules()
        );

        //if validation fails
        if($validator->fails()) {
            
            $errors = [];
            foreach($validator->errors()->getMessages() as $fieldName => $msgArr) {
                $errors[$fieldName] = $msgArr[0];
            }
            return $this->api->json(false, 'VALIDATION_ERROR', 'Fill all the fields before create trip', [
                'errors' => $errors
            ]);
        }


        //find correct trip
        $trip = $this->trip->where('id', $request->trip_id)
        ->where('driver_id', $request->auth_driver->id)
        ->first();


        //if no trip found means driver not allowed to add trip
        if(!$trip) {
            return $this->api->json(false, "INVALID", 'You are not allowed to add new pickup pionts to this');
        }

        //find trip parent pickup point
        $tripPointParent = $this->tripPoint->where('trip_id', $trip->id)
        ->where('trip_points_parent_id', 0)
        ->first();


        $tripPoint = new $this->tripPoint;
        $tripPoint->trip_id = $trip->id;
        $tripPoint->trip_points_parent_id = $tripPointParent->id;
        $tripPoint->seats_booked = 0;
        $tripPoint->source_address = trim($request->address);
        $tripPoint->source_latitude = $request->latitude;
        $tripPoint->source_longitude = $request->longitude;
        $tripPoint->destination_address = $tripPointParent->destination_address;
        $tripPoint->destination_latitude = $tripPointParent->destination_latitude;
        $tripPoint->destination_longitude = $tripPointParent->destination_longitude;
        $tripPoint->estimated_trip_time = $request->estimated_trip_time;
        $tripPoint->estimated_trip_distance = $request->estimated_trip_distance;

        /**
         * get ride fare for further ride calculation
         */
        $vTypeId = $this->vehicleType->getIdByCode($request->auth_driver->vehicle_type);
        $rideFare = $this->rideFare->where('vehicle_type_id', $vTypeId)->first();

        $fare = $rideFare->calculateFare($request->estimated_trip_distance, $request->estimated_trip_time);
        $tripPoint->estimated_fare = $fare['total'];

        $tripPoint->trip_status = TripModel::INITIATED;
        $tripPoint->save();      

        return $this->api->json(true, 'TRIP_POINT_ADDED', 'Trip point Added', [
            'trip_point' => $tripPoint
        ]);

    }





    /**
     * get all trips those are not completed
     */
    public function getTrips(Request $request)
    {

        $trips = $this->trip
        ->with('pickupPoints', 'pickupPoints.userBookings', 'pickupPoints.userBookings.user')
        ->where('driver_id', $request->auth_driver->id)
        ->whereNotIn('trip_status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED]);

        if(!$date = $this->trip->createDate($request->date)) {
            $trips = $trips->where('trip_date_time', 'like', $date->toDateString().'%');
        }
        //else take all trips after current date
        else {
            $trips = $trips->where('trip_date_time', '>=', date('Y-m-d H:i:s'));
        }

        $trips = $trips->get();

        return $this->api->json(true, 'TRIPS', 'Your trips', [
            'trips' => $trips
        ]);

    }




}
