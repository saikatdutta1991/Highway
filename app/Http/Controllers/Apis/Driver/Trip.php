<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Models\Trip\AdminTripRoute;
use App\Http\Controllers\Controller;
use DB;
use App\Jobs\ProcessUserRating;
use Illuminate\Http\Request;
use App\Repositories\SocketIOClient;
use App\Models\Trip\Trip as TripModel;
use App\Models\Trip\TripBooking;
use App\Models\Trip\TripPoint;
use App\Repositories\Utill;


use App\Repositories\Email;
use App\Models\Setting;
use App\Models\RideRequestInvoice as RideInvoice;
use App\Models\Transaction;
use App\Models\VehicleType;
use Validator;
use Carbon\Carbon;
use App\Jobs\ProcessDriverInvoice;

class Trip extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, AdminTripRoute $adminTripRoute)
    {
        $this->api = $api;
        $this->adminTripRoute = $adminTripRoute;
        $this->trip = app('App\Models\Trip\Trip');
        $this->adminTripLocation = app('App\Models\Trip\AdminTripLocation');
        $this->tripPoint = app('App\Models\Trip\TripPoint');
        $this->utill = app('App\Repositories\Utill');
        $this->tripBooking = app('App\Models\Trip\TripBooking');
        $this->socketIOClient = app('App\Repositories\SocketIOClient');
    }



    /**
     * get all trip locations
     */
    public function getAllLocations()
    {
        $locations = $this->adminTripLocation->orderBy('name', 'asc')->get()->toArray();
        return $this->api->json(true, 'LOCATIONS', 'Locations fetched', [
            'locations' => $locations
        ]);
    }


    /**
     * get admin trip route with location and location points
     */
    public function getTripRoutes(Request $request)
    {
        $routeRecord = $this->adminTripRoute
            ->with(['from', 'from.points', 'to', 'to.points'])
            ->where('from_location', $request->from_location)
            ->where('to_location', $request->to_location)
            ->where('is_ac_enabled', true)
            ->first();


        $routeDetails = [];
        $routeDetails['source'] = $routeRecord->from->name;
        $routeDetails['destination'] = $routeRecord->to->name;
        $routeDetails['time'] = $routeRecord->time;
        $routeDetails['base_fare'] = $routeRecord->base_fare;
        $routeDetails['access_fee'] = $routeRecord->access_fee;
        $routeDetails['total_fare'] = $routeRecord->total_fare;
        $routeDetails['status'] = $routeRecord->status;
        $routeDetails['source_points'] = $routeRecord->from->points->toArray();
        $routeDetails['destination_points'] = $routeRecord->to->points->toArray();
        $routeDetails['ac_enabled_routeid'] = $routeRecord->id;
        $routeDetails['nonac_enabled_routeid'] = $this->adminTripRoute->where('is_ac_enabled', false)->where('from_location', $request->from_location)->where('to_location', $request->to_location)->first()->id;

        return $this->api->json(true, 'ROUTES', 'Routes fetched', [
            'route' => $routeDetails
        ]);
        
    }




    /**
     * create new trip 
     * takes no of seats, trip date time, admin trip route id, admin trip route point ids
     */
    public function createTrip(Request $request)
    {

        /** check driver vehicle/service type is allowed to create trip */
        if(!VehicleType::where('code', $request->auth_driver->vehicle_type)->where('is_highway_enabled', true)->exists()) {
            return $this->api->json(false, 'INVALID_SERVICE_TYPE', 'Sorry, Your service type is not allowed to create highway trip');
        }
        



        /** validating request other */
        $validator = Validator::make(
            $request->all(), [
                'route_id' => 'required|exists:'.$this->adminTripRoute->getTableName().',id',
                'name' => 'required|max:256|min:1',
                'seats' => 'required|numeric',
                'date_time' => 'required|date_format:Y-m-d H:i:s'
            ]
        );

        if($validator->fails()) {
            
            $errors = [];
            foreach($validator->errors()->getMessages() as $fieldName => $msgArr) {
                $errors[$fieldName] = $msgArr[0];
            }
            return $this->api->json(false, 'VALIDATION_ERROR', 'Fill all the fields before create trip', [
                'errors' => $errors
            ]);
        }
        /**end validating request other */

    
        /* check source and destination points */
        $adminRoute = $this->adminTripRoute->find($request->route_id);
        $frmPointIds = explode(',', $request->from_point_ids); //source point ids
        $toPointIds = explode(',', $request->to_point_ids); //destination point ids
        $routePoints = array_merge(
            $adminRoute->from->points->pluck('id')->toArray(),
            $adminRoute->to->points->pluck('id')->toArray()
        );
        
        if(!count(array_intersect($routePoints, array_merge($frmPointIds, $toPointIds))) == count($routePoints)) {
            return $this->api->json(false, 'INVALID_POINTS', 'Invalid points');
        }
        /* end check source and destination points */


        /** dont allow driver creating trip if same day 5 hours trip created */
        $tripDatetimeObject = $this->utill->timestampStringToUTC($request->date_time, $request->auth_driver->timezone);
        $allowedDatetimeRange = [$tripDatetimeObject->subHour(5)->toDateTimeString(), $tripDatetimeObject->addHour(10)->toDateTimeString()];
        
        $previousTripCount = $this->trip
            ->join(
                $this->adminTripRoute->getTableName(), 
                $this->adminTripRoute->getTableName().'.id', 
                $this->trip->getTableName().'.admin_route_ref_id'
            )
            ->select([$this->trip->getTableName().'.*', $this->adminTripRoute->getTableName().'.from_location', $this->adminTripRoute->getTableName().'.to_location'])
            ->where($this->adminTripRoute->getTableName().'.from_location', $adminRoute->from_location)
            ->where($this->trip->getTableName().'.driver_id', $request->auth_driver->id)
            ->whereNotIn($this->trip->getTableName().'.status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED_DRIVER])
            ->whereBetween($this->trip->getTableName().'.trip_datetime', $allowedDatetimeRange)
            ->where($this->trip->getTableName().'.trip_datetime', '>', Carbon::now()->subMinutes(30)->toDateTimeString())
            ->count();


        if($previousTripCount) {
            return $this->api->json(false, 'TRIP_CREATE_NOT_ALLOWED', 'You are not allowed to create trip at this time since each trip must have minimum 5 hours gap.');
        }


        /** store trip details */
        $trip = new $this->trip;
        $trip->driver_id = $request->auth_driver->id;
        $trip->name = $request->name;
        $trip->from = $adminRoute->from->name;
        $trip->to = $adminRoute->to->name;
        $trip->seats = $request->seats;
        $trip->seats_available = $request->seats;
        $trip->admin_route_ref_id = $adminRoute->id;
        $trip->status = TripModel::CREATED;
        $trip->is_ac_enabled = $adminRoute->is_ac_enabled;
        $trip->trip_datetime = $this->utill->timestampStringToUTC($request->date_time, $request->auth_driver->timezone)->toDateTimeString();


        //to store create trip points
        $createdTripPts = [];
  
        try {

            DB::beginTransaction();
            
            $trip->save();

            /**insert source points from request form point ids */
            foreach($frmPointIds as $pid) {
                
                $point = $adminRoute->from->points->where('id', $pid)->first();
             
                $spt = new $this->tripPoint;
                $spt->trip_id = $trip->id;
                $spt->address = $point->address;
                $spt->label = $point->label;
                $spt->latitude = $point->latitude;
                $spt->longitude = $point->longitude;
                $spt->tag = 'SOURCE';
                $spt->status = TripPoint::CREATED;
                $spt->save();

                $createdTripPts[] = $spt; //push newly create trip point array
            }


            /**insert destination points from request to point ids */
            foreach($toPointIds as $pid) {
                
                $point = $adminRoute->to->points->where('id', $pid)->first();
                
                $spt = new $this->tripPoint;
                $spt->trip_id = $trip->id;
                $spt->address = $point->address;
                $spt->label = $point->label;
                $spt->latitude = $point->latitude;
                $spt->longitude = $point->longitude;
                $spt->tag = 'DESTINATION';
                $spt->status = TripPoint::CREATED;
                $spt->save();

                $createdTripPts[] = $spt; //push newly create trip point array
            }


            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('CREATE_TRIP_ERROR', $e->getMessage());
            return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }



        return $this->api->json(true, "TRIP_CREATED", 'Trip created', [
            'trip' => $trip,
            'trip_points' => $createdTripPts
        ]);


    }



    /**
     * get all trips
     */
    public function getTrips(Request $request)
    {

        $driver = $request->auth_driver;

        $trips = $this->trip
        ->where('driver_id', $driver->id)
        ->orderBy('trip_datetime', 'desc')
        // remove trips created but not started of previous date
        ->where(function($query){

            $query->where('trip_datetime', ">", Carbon::now()->subMinutes(30)->toDateTimeString())
            ->orWhere(function($query){
                $query->where('trip_datetime', "<=", date('Y-m-d H:i:s'))
                ->where('status', '<>', TripModel::CREATED);
            });

        })
        ->paginate(500);

        $trips->map(function($trip) use($driver){
            $trip['local_trip_datetime'] = $trip->formatedJourneyDate($driver->timezone);
        });

        return $this->api->json(true, 'TRIPS', 'Your trips', [
            'trips' => $trips->items(),
             'paging' => [
                'total' => $trips->total(),
                'has_more' => $trips->hasMorePages(),
                'next_page_url' => $trips->nextPageUrl()?:'',
                'count' => $trips->count(),
            ]
        ]);

    }


    /**
     * gets a particular trip details
     * after this driver can start tirp
     */
    public function getTripDetails(Request $request)
    {
        $trip = $this->trip
        ->where('driver_id', $request->auth_driver->id)
        ->where("id", $request->trip_id)
        ->with(['points', 
        'points.boardingBookings' => function($query){
            $query->whereNotIn('booking_status', [TripBooking::INITIATED, TripBooking::BOOKING_CANCELED_USER]);
        }, 
        'points.destBookings' => function($query){
            $query->whereNotIn('booking_status', [TripBooking::INITIATED, TripBooking::BOOKING_CANCELED_USER]);
        },
        'points.boardingBookings.user', 
        'points.destBookings.user'
        ])
        ->first();


        
        /** remove points where no users boarding or unborading*/
        $pointsToTake = [];
        foreach ($trip->points as $point) {           

            if(!($point->boardingBookings->count() == 0 && $point->destBookings->count() == 0)) {
                 $pointsToTake[] = $point;
            }

        }
        
        unset($trip->points);
        $trip->points = collect($pointsToTake);
        

        if(!$trip) {
            return $this->api->json(false, "INVALID_TRIP_ID", 'Invalid trip id');
        }

        return $this->api->json(true, 'TRIP', 'Trip details', [
            'trip' => $trip
        ]);
        
    }



    /**
     * start specific trip by trip id
     */
    public function startTrip(Request $request)
    {

        /** check any trip already started or not  */
        $ongoingTripCount = $this->trip
        ->where('driver_id', $request->auth_driver->id)
        ->where('status', TripModel::TRIP_STARTED)
        ->count();

        if($ongoingTripCount) {
            return $this->api->json(false, "MULTIPLE_TRIP_CANTBE_STARTED", "Multiple trip cannot be started");
        }



        $trip = $this->trip
        ->where('driver_id', $request->auth_driver->id)
        ->where("id", $request->trip_id)
        ->where('status', TripModel::CREATED)
        ->with('bookings', 'bookings.user')
        ->first();

        if(!$trip) {
            return $this->api->json(false, "INVALID_TRIP", 'Invalid trip');
        }



        /** don't let the driver to start trip if no user booked */
        if(!$this->tripBooking->where('trip_id', $trip->id)->where('booking_status', TripBooking::BOOKING_CONFIRMED)->count()) {
            return $this->api->json(false, "NO_USER_BOOKED", 'No users have booked, so you can\'t start trip');
        }





        /** remove all INITIALTED bookings */
        $this->tripBooking->where('trip_id', $trip->id)->where('booking_status', TripBooking::INITIATED)->forceDelete();



        try {

            DB::beginTransaction();
            
            
            //change trip status to driver started the trip
            $trip->status = TripModel::TRIP_STARTED;
            $trip->start_time = date('Y-m-d H:i:s');
            $trip->save();

            //trip points status DRIVER_STARTED
            $this->tripPoint->where('trip_id', $trip->id)->update([
                'status' => TripPoint::DRIVER_STARTED
            ]);

            //making driver unavailbable
            $driver = $request->auth_driver;
            $driver->is_available = 0;
            $driver->save();
            
            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('DRIVER_STARTED_TRIP', $e->getMessage());
            return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }

        /** send socket events to all users */
        $userIds = $trip->bookings->pluck('user_id')->toArray();
        if(!empty($userIds)) {
            $userIds = implode(',', $userIds);
            $this->socketIOClient->sendEvent([
                'to_ids' => $userIds,
                'entity_type' => 'user', //socket will make it uppercase
                'event_type' => 'trip_status_changed',
                'data' => [
                    'type' => 'trip_started',
                    'trip' => $trip
                ]
            ]);
        }
        
        
        /** send push notification to all users */  
        $msgTitle = "Trip {$trip->name} started";
        $msg = "{$trip->name} started. We will notify you as driver reaches pickup location.";
        foreach($trip->bookings as $booking) {
            $booking->user->sendPushNotification($msgTitle, $msg);
            $booking->user->sendSms($msg." Track your booking here ".$booking->trackBookingUrl());
        }
    

        return $this->api->json(true, 'TRIP_STARTED', 'Trip started');
    }





    /**
     * when driver reaches a particular trip point
     * presses point reached
     * all boarding passengers gets notofication
     * all unboarding passengers invoice, trip_end status
     * trip route and booking user status will be driver reached
     */
    public function driverReachedTripPoint(Request $request)
    {
        $point = $this->tripPoint->where('trip_id', $request->trip_id)
        ->where('id', $request->point_id)
        //->where('status', TripPoint::DRIVER_STARTED)
        ->first();

        /** validate trip belongs to driver */
        if(!$point || $point->trip->driver_id != $request->auth_driver->id) {
            return $this->api->json(false, "INVALID_TRIP", 'Invalid trip');
        }

        
        $point->status = TripPoint::DRIVER_REACHED;
        $point->reached_time = date('Y-m-d H:i:s');
        $point->save();

        $trip = $point->trip;

        //if all points driver reached then trip ended
        $reachedCount = $this->tripPoint->where('trip_id', $trip->id)->where('status', TripPoint::DRIVER_REACHED)->count();

        $minPointsTobeReachedCount = 0;
        foreach ($trip->points as $pointKey => $p) {          
            if($p->boardingBookings->count() > 0 || $p->destBookings->count() > 0) {
                ++$minPointsTobeReachedCount;
            }
        }

        if($reachedCount >= $minPointsTobeReachedCount) {
            $trip->status = TripModel::TRIP_ENDED;
            $trip->save();
        }
        

        /** send socket and push notifications to boarding users */
        $userIds = [];
        $pushTitle = "Trip {$trip->name} driver reached";
        $pushMsg = "Driver has reached your boarding point, You can contact the driver.";
      
        foreach($point->boardingBookings as $booking) {
            $booking->user->sendPushNotification($pushTitle, $pushMsg);
            $userIds[] = $booking->user->id;
        }

        $userIds = implode(',', $userIds);
        $this->socketIOClient->sendEvent([
            'to_ids' => $userIds,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'trip_status_changed',
            'data' => [
                'type' => 'driver_reached_point',
                'trip' => $trip
            ]
        ]);
        /** end send socket and push notifications to boarding users */



        /** send socket and push notifications to unboarding users */
        $userIds = [];
        $pushTitle = "Trip {$trip->name} ended";
        $pushMsg = "You have reached your destination. Hope you enjoyed your journey.";
      
        foreach($point->destBookings as $booking) {
            $booking->user->sendPushNotification($pushTitle, $pushMsg);
            $userIds[] = $booking->user->id;
        }

        $userIds = implode(',', $userIds);
        $this->socketIOClient->sendEvent([
            'to_ids' => $userIds,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'trip_status_changed',
            'data' => [
                'type' => 'driver_reached_point',
                'trip' => $trip
            ]
        ]);
        /** end send socket and push notifications to boarding users */


        return $this->api->json(true, 'TRIP_POINT_REACHED', 'Trip point reached', [
            'message' => 'Get trip details again. Dont show this message to driver'
        ]);


    }




    /**
     * boarding user
     */
    public function userBoarded(Request $request)
    {

        $userIds = explode(',', $request->user_ids);
        $userIds = array_filter($userIds);


        $bookings = $this->tripBooking->where('trip_id', $request->trip_id)
        ->whereIn('user_id', $userIds)
        ->where('booking_status', TripBooking::BOOKING_CONFIRMED)
        ->where('boarding_time', null)
        ->with('trip', 'user')
        ->get();


        if(!$bookings->count()) {
            return $this->api->json(false, "INVALID_TRIP", 'Invalid trip');
        }
        

        /** loop thorugh all bookings */
        foreach($bookings as $booking) {

            /** validate trip belongs to driver */
            if(!$booking || $booking->trip->driver_id != $request->auth_driver->id) {
                return $this->api->json(false, "INVALID_TRIP", 'Invalid trip');
            }

            $booking->boarding_time = date('Y-m-d H:i:s');
            $booking->save();

            /** send push notification and sokcet notification to user */
            $pushTitle = "Trip {$booking->trip->name} boarded";
            $pushMsg = "You have boarded to trip";     
            $booking->user->sendPushNotification($pushTitle, $pushMsg);


        }

        
        /** send socket and push notifications to boarding users */
        $this->socketIOClient->sendEvent([
            'to_ids' => $userIds,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'trip_status_changed',
            'data' => [
                'type' => 'boarded',
                'trip' => $bookings[0]->trip
            ]
        ]);
        /** end send socket and push notifications to boarding users */


        return $this->api->json(true, 'USER_BOARDED', 'Users boarded', [
            'message' => 'Get trip details again. Dont show this message to driver'
        ]);


    }



    /**
     * driver gives rating to users
     */
    public function driverGiveRatingToBookedUsers(Request $request)
    {

        $booking = $this->tripBooking->where('trip_id', $request->trip_id)
        ->where('user_id', $request->user_id)
        ->where('booking_status', TripBooking::BOOKING_CONFIRMED)
        ->first();

        /** validate trip belongs to driver */
        if(!$booking || $booking->trip->driver_id != $request->auth_driver->id || !in_array($request->rating, TripBooking::RATINGS)) {
            return $this->api->json(false, "INVALID_TRIP", 'Invalid trip');
        }        
       
        $booking->user_rating = $request->rating;
        $booking->save();

        /** push user rating calculation to job */
        ProcessUserRating::dispatch($booking->user_id);

        return $this->api->json(true, 'RATING_DONE', 'Rating done');

    }



    /**
     * cancel trip
     */
    public function cancelTrip(Request $request)
    {
        $trip = $this->trip
        ->where('driver_id', $request->auth_driver->id)
        ->where("id", $request->trip_id)
        ->where('status', TripModel::CREATED)
        ->with('bookings', 'bookings.user')
        ->first();

        if(!$trip) {
            return $this->api->json(false, "INVALID_TRIP", 'Invalid trip');
        }

        //cancel only booking trips
        try {

            DB::beginTransaction();

            $trip->status = TripModel::TRIP_CANCELED_DRIVER;
            $trip->save();
            
            $bookings = $this->tripBooking->where('trip_id', $trip->id)
            ->where('booking_status', TripBooking::BOOKING_CONFIRMED)
            ->get();

            foreach($bookings as $booking) {
                $booking->booking_status = TripModel::TRIP_CANCELED_DRIVER;
                $booking->save();
                
                $user = $booking->user;
                $message = Utill::transMessage('app_messages.trip_cancel_driver_message', ['tripname' => $trip->name]);
                $title = Utill::transMessage('app_messages.trip_cancel_driver_title');
                $user->sendPushNotification($title, $message);
                $user->sendSms($message);
            }


            
            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('DRIVER_STARTED_TRIP', $e->getMessage());
            return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }

        ProcessDriverInvoice::dispatch('highway', $trip->id);

        return $this->api->json(true, "TRIP_CANCELED", "Trip canceled successfully");
    }








    // /**
    //  * delete a particular trip if only if trip status is initiated
    //  */
    // public function deleteTrip(Request $request)
    // {
    //     //fetching correct trip to delete
    //     $trip = $this->trip->where('id', $request->trip_id)
    //     ->where('driver_id', $request->auth_driver->id)
    //     ->where('status', TripModel::INITIATED)
    //     ->first();

    //     //if no trip found means driver not allowed to delete
    //     if(!$trip) {
    //         return $this->api->json(false, "INVALID", 'You are not allowed to delete this trip any more. You can cancel it.');
    //     }


    //     try {

    //         DB::beginTransaction();
            
    //         $this->trip->deleteTrip($trip->id);
            
    //         DB::commit();

    //     } catch(\Exception $e) {
    //         DB::rollback();
    //         $this->api->log('DELETE_TRIP_ERROR', $e->getMessage());
    //         return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
    //     }

    //     return $this->api->json(true, 'TRIP_DELETED', 'Trip deleted');
        
    // }






    




    /**
     * complete trip
     * if user rating done all 
     * if all trip routes status trip_ended
     */
    public function completeTrip(Request $request)
    {
        $trip = $this->trip
        ->where('driver_id', $request->auth_driver->id)
        ->where("id", $request->trip_id)
        ->where('status', TripModel::TRIP_ENDED)
        ->with('points', 'bookings')
        ->first();


        if(!$trip) {
            return $this->api->json(false, "INVALID_TRIP", 'Invalid trip');
        }

        
        /** check all points reached */
        /* if($trip->points->where('status', TripPoint::DRIVER_REACHED)->count() != $trip->points->count()) {
            return $this->api->json(false, 'TRIP_POINTS_NOT_REACHED', 'All trip points not reached');
        } */

        /**check booking users rating done */
        /* if($trip->bookings->where('user_rating', 0)->where('booking_status', TripBooking::BOOKING_CONFIRMED)->count()) {
            return $this->api->json(false, 'TRIP_BOOINKG_RATINGS_NOT_DONE', 'All the user bookings not rated');
        } */


        $trip->status = TripModel::COMPLETED;
        $trip->end_time = date('Y-m-d H:i:s');
        $trip->save();
        
        $driver = $request->auth_driver;
        $driver->is_available = 1;
        $driver->save();


        ProcessDriverInvoice::dispatch('highway', $trip->id);

        return $this->api->json(true, 'TRIP_COMPLETED', 'Trip completed');


    }



}
