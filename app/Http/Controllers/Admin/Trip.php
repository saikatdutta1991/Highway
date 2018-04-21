<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\Models\Setting as Set;
use App\Models\AdminTrip;
use App\Models\AdminTripPoint;
use App\Models\AdminTripRoute;
use Validator;


class Trip extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Set $setting, Api $api, AdminTrip $trip, AdminTripPoint $tripPoint, AdminTripRoute $tripRoute)
    {
        $this->setting = $setting;
        $this->api = $api;
        $this->trip = $trip;
        $this->tripPoint = $tripPoint;
        $this->tripRoute = $tripRoute;
    }



    /**
     * shows add trip route page
     */
    public function showAddTripRoute(Request $request)
    {
        $setting = $this->setting;
        return view('admin.trips.add_new_route', compact('setting'));
    }


    /**
     * adds new trip route
     */
    public function addNewTripRoute(Request $request)
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
        $trip->name = ucfirst(trim($request->name));

        try {

            DB::beginTransaction();
            
            $trip->save();

            //saving trip points
            $tripPoints = [];
            /* $tripRoute */
            $order = 1;
            foreach($request->points as $point) {
                $tripPoint = new $this->tripPoint;
                $tripPoint->admin_trip_id = $trip->id;
                $tripPoint->order = $order++;
                $tripPoint->address = $point['address'];
                $tripPoint->latitude = $point['latitude'];
                $tripPoint->longitude = $point['longitude'];
                $tripPoint->distance = isset($point['distance']) ? $point['distance'] / 1000 : 0;
                $tripPoint->time = isset($point['time']) ? $point['time'] : 0;
                $tripPoint->fare = isset($point['fare']) ? $point['fare'] : 0.00;
                $tripPoint->save(); 
                $tripPoints[] = $tripPoint;
            }
        
            /**
             * finding all possible routes and distance, time estimated
             */
            $tripRoutes = [];
            $pointsCount = count($tripPoints);
            $scaleSize = 2;
            while($scaleSize <= $pointsCount) {

                $scaleStartIndex = 0;
                $scaleEndIndex = $scaleStartIndex + $scaleSize - 1;

                while($scaleEndIndex < $pointsCount) {
                    
                    $fare = 0;
                    $time = 0;
                    $distance = 0;
                    for($i = $scaleStartIndex; $i < $scaleEndIndex; $i++) {
            
                        $tripPoint = $tripPoints[$i + 1];
                        $time += $tripPoint->time;
                        $distance += $tripPoint->distance;
                        $fare += $tripPoint->fare;
                    }

                    /**
                     * save trip routes in database
                     */
                    //echo "{$tripPoints[$scaleStartIndex]->address} -  {$tripPoints[$scaleEndIndex]->address} $time $distance<br>";
                    $tripRoute = new $this->tripRoute;
                    $tripRoute->admin_trip_id = $trip->id;
                    $tripRoute->start_point_address = $tripPoints[$scaleStartIndex]->address;
                    $tripRoute->start_point_latitude = $tripPoints[$scaleStartIndex]->latitude;
                    $tripRoute->start_point_longitude = $tripPoints[$scaleStartIndex]->longitude;
                    $tripRoute->start_point_order = $tripPoints[$scaleStartIndex]->order;
                    $tripRoute->end_point_address = $tripPoints[$scaleEndIndex]->address;
                    $tripRoute->end_point_latitude = $tripPoints[$scaleEndIndex]->latitude;
                    $tripRoute->end_point_longitude = $tripPoints[$scaleEndIndex]->longitude;
                    $tripRoute->end_point_order = $tripPoints[$scaleEndIndex]->order;
                    $tripRoute->seat_affects = '';
                    $tripRoute->estimated_distance = $distance;
                    $tripRoute->estimated_time = $time;
                    $tripRoute->estimated_fare = $fare;

                    $tripRoute->save();
                    $tripRoutes[] = $tripRoute;
                    
                    $scaleStartIndex++;
                    $scaleEndIndex++;

                }

                $scaleSize++;

            }


            /**
             * calculate seat affects and save
             */
            $this->tripRoute->calculateSeatAffects($tripRoutes);
         
        
            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('CREATE_TRIP_ERROR', $e->getMessage());
            return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }
        
        
        
        return $this->api->json(true, "TRIP_CREATED", 'Trip created', [
            'trip' => $trip,
            'trip_points' => $tripPoints,
            'trip_routes' => $tripRoutes
        ]);
    }



    /**
     * show trip points
     */
    public function showTripPoints(Request $request)
    {
        $points = $this->tripPoint->orderBy('created_at', 'desc');

        /** specific city */
        if($request->city != "") {
            $points = $points->where('city', 'like', $request->city);
        }

        /** specific country */
        if($request->country != "") {
            $points = $points->where('country', 'like', $request->country);
        }

        $points = $points->paginate(100);
        return view('admin.trips.show_trip_points', compact('points'));
    }



    /**
     * show add new trip point(only one single point for trips)
     */
    public function showAddPoint()
    {
        $setting = $this->setting;
        return view('admin.trips.add_new_point', compact('setting'));
    }



    /**
     * add new trip point
     */
    public function addNewPoint(Request $request)
    {
        list($latRegex, $longRegex) = app('UtillRepo')->regexLatLongValidate();
        $validator = Validator::make($request->all(), [
            'address' => 'required|min:1|max:500', 
            'city' => 'required|min:1|max:100', 
            'country' => 'required|min:1|max:100', 
            'zip_code' => 'required|min:1|max:100', 
            'latitude' => ['required', 'regex:'.$latRegex], 
            'longitude' => ['required', 'regex:'.$longRegex],
        ]);

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


        $tripPoint = new $this->tripPoint;
        $tripPoint->address = $request->address;
        $tripPoint->latitude = $request->latitude;
        $tripPoint->longitude = $request->longitude;
        $tripPoint->city = $request->city;
        $tripPoint->country = $request->country;
        $tripPoint->zip_code = $request->zip_code;
        $tripPoint->save();

        return $this->api->json(true, "TRIP_POINT_ADDED", 'Trip point added', [
            'trip_point' => $tripPoint
        ]);

    }



    /**
     * delete trip point
     */
    public function deleteTripPoint(Request $request)
    {
        $point = $this->tripPoint->find($request->point_id);
        if($point) {
            $point->delete();
        }

        return $this->api->json(true, 'POINT_DELETED', 'Point deleted');
    }




}
