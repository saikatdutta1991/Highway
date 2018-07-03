<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Models\Trip\AdminTripRoute;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\Models\Trip\Trip as TripModel;
use App\Models\Trip\TripPoint;
use App\Repositories\Utill;


use App\Repositories\Email;
use App\Models\Setting;
use App\Repositories\SocketIOClient;
use App\Models\VehicleType;
use App\Models\RideFare;
use App\Models\RideRequestInvoice as RideInvoice;
use App\Models\Transaction;
use App\Models\UserTrip;
use Validator;

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
        $this->tripPoint = app('App\Models\Trip\TripPoint');
        $this->utill = app('App\Repositories\Utill');
    }


    /**
     * get admin trip routes with location and location points
     */
    public function getTripRoutes()
    {
        $routes = $this->adminTripRoute->with(['from', 'from.points', 'to', 'to.points'])->orderBy('updated_at', 'desc')->get();

        $routes->map(function($route){
            $route->source = $route->from->name;
            $route->destination = $route->to->name;

            $route->source_points = $route->from->points;
            $route->destination_points = $route->to->points;

            unset($route->from->points);
            unset($route->from);
            unset($route->to->points);
            unset($route->to);
        });


        return $this->api->json(true, 'ROUTES', 'Routes fetched', [
            'routes' => $routes
        ]);
        
    }




    /**
     * create new trip 
     * takes no of seats, trip date time, admin trip route id, admin trip route point ids
     */
    public function createTrip(Request $request)
    {

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

        $trips = $this->trip
        ->where('driver_id', $request->auth_driver->id)
        ->orderBy('trip_datetime', 'desc')
        ->paginate(200);

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




    




    // /**
    //  * gets a particular trip details
    //  * after this driver can start tirp
    //  */
    // public function getTripDetails(Request $request)
    // {
    //     $trip = $this->trip
    //     ->where('driver_id', $request->auth_driver->id)
    //     ->where("id", $request->trip_id)
    //     ->first();

    //     if(!$trip) {
    //         return $this->api->json(false, "INVALID_TRIP_ID", 'Invalid trip id');
    //     }

    //     $tripPoints = $trip->tripPoints;
    //     $tripRoutes = $trip->tripRoutes;

    //     //find how many boarding and unboarding users for a particular point
    //     foreach($tripPoints as $index => $tripPoint) {

    //         //find how many trip routes start from specific point
    //         $tripRouteIds = $trip->tripRoutes->where('start_point_order', $tripPoint->order)->pluck('id')->all();
            
    //         //find how many user bookings made for all triprouteids
    //         $userTripBookings = $this->userTrip->whereIn('trip_route_id', $tripRouteIds)->with('user')->get();

    //         $tripPoints[$index]['boarding_user_bookings'] = $userTripBookings;
    //         $tripPoints[$index]['total_boarding_users_count'] = $userTripBookings->sum('no_of_seats_booked');


    //          //find how many trip routes end from specific point
    //         $tripRouteIds = $trip->tripRoutes->where('end_point_order', $tripPoint->order)->pluck('id')->all();
            
    //         //find how many user bookings made for all triprouteids
    //         $userTripBookings = $this->userTrip->whereIn('trip_route_id', $tripRouteIds)->with('user')->get();

    //         $tripPoints[$index]['unboarding_user_bookings'] = $userTripBookings;
    //         $tripPoints[$index]['total_unboarding_users_count'] = $userTripBookings->sum('no_of_seats_booked');



    //     }


        
    //     unset($trip->tripRoutes);
    //     unset($trip->tripPoints);
        
    //     return $this->api->json(true, 'TRIP', 'Trip details', [
    //         'trip' => $trip,
    //         'trip_points' => $tripPoints,
    //         'tirp_routes' => $tripRoutes
    //     ]);


    // }





    // /**
    //  * driver start a trip
    //  * DRIVER_STARTED
    //  */
    // public function driverStartedTrip(Request $request)
    // {
    //     $trip = $this->trip
    //     ->where('driver_id', $request->auth_driver->id)
    //     ->where("id", $request->trip_id)
    //     ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED])
    //     ->first();

    //     if(!$trip) {
    //         return $this->api->json(false, "INVALID_TRIP_ID", 'Invalid trip id');
    //     }


    //     try {

    //         DB::beginTransaction();
            
    //         //change trip status to driver started the trip
    //         $trip->status = TripModel::DRIVER_STARTED;
    //         $trip->save();

    //         //update trip_routes status to driver started the trip
    //         $this->tripRoute->where('trip_id', $trip->id)->update(['status' => TripModel::DRIVER_STARTED]);

    //         //update user bookings status to driver started the trip
    //         $this->userTrip->where('trip_id', $trip->id)->update(['status' => TripModel::DRIVER_STARTED]);

    //         //making driver un availbable
    //         $authDriver = $request->auth_driver;
    //         $authDriver->is_available = 0;
    //         $authDriver->save();
            
    //         DB::commit();

    //     } catch(\Exception $e) {
    //         DB::rollback();
    //         $this->api->log('DRIVER_STARTED_TRIP', $e->getMessage());
    //         return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
    //     }

        

    //     //send all user push notification that driver has started the trip
    //     $userIds = [];
    //     $bookings = $this->userTrip->where('trip_id', $trip->id)->with('user')->get();
    //     foreach($bookings as $booking) {
    //         $booking->user->sendPushNotification("Trip {$trip->name} has been started", "Driver has started the trip, you will be notified as soon driver reaches your pickup point");
    //         $userIds[] = $booking->user->id;
    //     }
        
    //     $userIds = implode(',', $userIds);
    //     $this->socketIOClient->sendEvent([
    //         'to_ids' => $userIds,
    //         'entity_type' => 'user', //socket will make it uppercase
    //         'event_type' => 'trip_booking_status_changed',
    //         'data' => [
    //             'type' => 'driver_started_trip',
    //             'trip' => $trip
    //         ]
    //     ]);


    //     return $this->api->json(true, 'TRIP_DRIVER_STARTED', 'Trip started');

    // }





    // /**
    //  * when driver reaches a particular trip point
    //  * presses point reached
    //  * all boarding passengers gets notofication
    //  * all unboarding passengers invoice, trip_end status
    //  * trip route and booking user status will be driver reached
    //  */
    // public function driverReachedTripPoint(Request $request)
    // {
    //     $trip = $this->trip
    //     ->where('driver_id', $request->auth_driver->id)
    //     ->where("id", $request->trip_id)
    //     ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED])
    //     ->first();

    //     $tripPoint = $this->tripPoint->where('trip_id', $trip->id)->where('id', $request->trip_point_id)->first();
    //     $tripPoint->status = TripModel::DRIVER_REACHED;
    //     $tripPoint->save();

    //     //find how many trip routes start from specific point
    //     $tripRouteIds = $trip->tripRoutes->where('start_point_order', $tripPoint->order)->pluck('id')->all();
    //     //update driver reached for all routes whose starting point is current trip point
    //     $this->tripRoute->where('trip_id', $trip->id)->where('start_point_order', $tripPoint->order)->update(['status' => TripModel::DRIVER_REACHED, 'reached_timestamp' => date('Y-m-d H:i:s')]);
    //     //update alluser bookings status driver reached
    //     $this->userTrip->wherein('trip_route_id', $tripRouteIds)->update(['status' => TripModel::DRIVER_REACHED]);
            
    //     //find how many user bookings made for all triprouteids ( boarding passengers)
    //     $boardingUserBookings = $this->userTrip->whereIn('trip_route_id', $tripRouteIds)->with('user')->get();

    //     /**
    //      * send all boarding passenders push notification 
    //      * that driver has reached boarding point
    //      */
    //     $userIds = [];
    //     foreach($boardingUserBookings as $booking) {
    //         $booking->user->sendPushNotification("Trip {$trip->name} : driver reached", "Driver has reached your boarding point {$tripPoint->address}, You can contact the driver.");
    //         $userIds[] = $booking->user->id;
    //     }

    //     $userIds = implode(',', $userIds);
    //     $this->socketIOClient->sendEvent([
    //         'to_ids' => $userIds,
    //         'entity_type' => 'user', //socket will make it uppercase
    //         'event_type' => 'trip_booking_status_changed',
    //         'data' => [
    //             'type' => 'driver_reached_point',
    //             'trip' => $trip,
    //             'trip_point' => $tripPoint
    //         ]
    //     ]);


    //     //find all trip routes end in specific point (current point)
    //     $unboaringRoutes = $trip->tripRoutes->where('end_point_order', $tripPoint->order);

    //     /**
    //      * get ride fare for further ride calculation
    //      */
    //     $vTypeId = $this->vehicleType->getIdByCode($request->auth_driver->vehicle_type);
    //     $rideFare = $this->rideFare->where('vehicle_type_id', $vTypeId)->first();

    //     foreach($unboaringRoutes as $route) {
            
    //         //update route trip_ended if trip_started
    //         $route->status = TripModel::TRIP_ENDED;
    //         $route->end_timestamp = date('Y-m-d H:i:s');
    //         $route->save();

    //         //find all unboarding user bookings
    //         $unboardingUserBookings = $this->userTrip->where('trip_route_id', $route->id)->with('user')->get();

    //         $fare = $rideFare->calculateFare($route->estimated_distance, $route->estimated_time);


    //         //for each user booking set invoice
    //         foreach($unboardingUserBookings as $booking) {
    //             //if cash then status completed
    //             $booking->status = TripModel::TRIP_ENDED;

    //             //creting invoice
    //             $invoice = new $this->rideInvoice;
    //             $invoice->invoice_reference = $this->rideInvoice->generateInvoiceReference();
    //             $invoice->payment_mode = $booking->payment_mode;
    //             $invoice->ride_fare = app('App\Repositories\Utill')->formatAmountDecimalTwo($fare['ride_fare'] * $booking->no_of_seats_booked); //multiplying with no of seats
    //             $invoice->access_fee = app('App\Repositories\Utill')->formatAmountDecimalTwo($fare['access_fee'] * $booking->no_of_seats_booked);
    //             $invoice->tax = app('App\Repositories\Utill')->formatAmountDecimalTwo($fare['taxes'] * $booking->no_of_seats_booked);
    //             $invoice->total = app('App\Repositories\Utill')->formatAmountDecimalTwo($fare['total'] * $booking->no_of_seats_booked);
    //             $invoice->currency_type = $this->setting->get('currency_code');

    //             list($invoiceImagePath, $invoiceImageName) = $invoice->saveGoogleStaticMap($route->start_point_latitude, $route->start_point_longitude, $route->end_point_latitutde, $route->end_point_longitude);
    //             $invoice->invoice_map_image_path = $invoiceImagePath;
    //             $invoice->invoice_map_image_name = $invoiceImageName;


    //              //if cash payment mode then payment_status paid
    //             if($booking->payment_mode == TripModel::CASH) {
    //                 $booking->payment_status = TripModel::PAID;
    //                 $booking->status = TripModel::COMPLETED;
    //                 $invoice->payment_status = TripModel::PAID;

    //                 //create transaction because payment successfull here
    //                 $transaction = new $this->transaction;
    //                 $transaction->trans_id = $invoice->invoice_reference;
    //                 $transaction->amount = $fare['total'];
    //                 $transaction->currency_type = $this->setting->get('currency_code');
    //                 $transaction->gateway = TripModel::CASH;
    //                 $transaction->payment_method = TripModel::COD;
    //                 $transaction->status = Transaction::SUCCESS;
    //                 $transaction->save();

    //                 //add transaciton_table_id in invoice
    //                 $invoice->transaction_table_id = $transaction->id;

    //             }
                
    //             $invoice->save();

    //             $booking->trip_invoice_id = $invoice->id;
    //             $booking->save();

    //             //send invoice if paid
    //             if($booking->payment_status == TripModel::PAID) {
    //                 $this->email->sendUserTripInvoiceEmail($booking);
    //             }

    //             /**
    //              * send push notification to user
    //              */
    //             $user = $booking->user;
    //             $currencySymbol = $this->setting->get('currency_symbol');
    //             $user->sendPushNotification("Your trip ended", "We hope you enjoyed our intercity trip service. Please make payment of {$currencySymbol}".$invoice->total);
    //             $user->sendSms("We hope you enjoyed our intercity trip service. Please make payment of {$currencySymbol}".$invoice->total);

    //             /**
    //              * send socket push to user
    //              */
    //             $this->socketIOClient->sendEvent([
    //                 'to_ids' => $user->id,
    //                 'entity_type' => 'user', //socket will make it uppercase
    //                 'event_type' => 'trip_booking_status_changed',
    //                 'data' => [
    //                     'type' => 'trip_end',
    //                     'trip' => $trip,
    //                     'trip_route' => $route,
    //                     'booking' => $booking,
    //                     'invoice' => $invoice->toArray(),
    //                 ]
    //             ]);

    //             /**
    //              * dont call invoice save method after this
    //              */
    //             $invoice->map_url = $invoice->getStaticMapUrl();


    //         }


    //     }


    //     return $this->api->json(true, 'TRIP_POINT_REACHED', 'Trip point reached', [
    //         'message' => 'Get trip details again. Dont show this message to driver'
    //     ]);


    // }



    // /**
    //  * start trip point
    //  * send all users boarding from that point 
    //  * take who all passengers are boarding
    //  */
    // public function driverStartTripPoint(Request $request)
    // {
    //     $trip = $this->trip
    //     ->where('driver_id', $request->auth_driver->id)
    //     ->where("id", $request->trip_id)
    //     ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED])
    //     ->first();

        
    //     //send notification to all users that your trip has started
    //     $tripPoint = $this->tripPoint->where('trip_id', $trip->id)->where('id', $request->trip_point_id)->first();
    //     $tripPoint->status = TripModel::TRIP_STARTED;
    //     $tripPoint->save();
    //     //find how many trip routes start from specific point
    //     $tripRouteIds = $trip->tripRoutes->where('start_point_order', $tripPoint->order)->pluck('id')->all();
    //     //update driver reached for all routes whose starting point is current trip point
    //     $this->tripRoute->where('trip_id', $trip->id)->where('start_point_order', $tripPoint->order)->update(['status' => TripModel::TRIP_STARTED, 'start_timestamp' => date('Y-m-d H:i:s')]);
    //     //update all user trips status trip started
    //     $this->userTrip->whereIn('trip_route_id', $tripRouteIds)->update(['status' => TripModel::TRIP_STARTED]);
            
    //     //find how many user bookings made for all triprouteids ( boarding passengers)
    //     $boardingUserBookings = $this->userTrip->whereIn('trip_route_id', $tripRouteIds)->with('user')->get();

    //     /**
    //      * send all boarding passenders push notification 
    //      * that driver has reached boarding point
    //      */
    //     $userIds = [];
    //     $boardingUserIds = explode(',', $request->boarding_user_ids);
    //     foreach($boardingUserBookings as $booking) {
            
    //         if(!empty($boardingUserIds) && in_array($booking->user->id, $boardingUserIds)) {
    //             $booking->is_boarded = 1; 
    //             $booking->save();
    //         }
    //         $booking->user->sendPushNotification("Trip {$trip->name} : started", "Driver has started trip at boarding point {$tripPoint->address}, You can contact the driver.");
    //         $userIds[] = $booking->user->id;
    //     }

    //     /**
    //      * send socket push to user
    //      */
    //     $this->socketIOClient->sendEvent([
    //         'to_ids' => implode(',', $userIds),
    //         'entity_type' => 'user', //socket will make it uppercase
    //         'event_type' => 'trip_booking_status_changed',
    //         'data' => [
    //             'type' => 'driver_started_from_point',
    //             'trip' => $trip,
    //             'trip_point' => $tripPoint,
    //         ]
    //     ]);


    //     return $this->api->json(true, 'TRIP_STARTED', 'Trip started', [
    //         'message' => 'Get trip details again. Dont show this message to driver'
    //     ]);

    // }




    // /**
    //  * driver gives rating to users
    //  */
    // public function driverGiveRatingToBookedUsers(Request $request)
    // {
    //     $trip = $this->trip
    //     ->where('driver_id', $request->auth_driver->id)
    //     ->where("id", $request->trip_id)
    //     ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED])
    //     ->first();

    //     if(!is_array($request->ratings)) {
    //         return $this->api->json(false, 'INVALID_RATINGS', 'Invalid rating array');
    //     }


    //     foreach($request->ratings as $rating) {
            
    //         $booking = $this->userTrip->where('trip_id', $trip->id)->where('user_id', $rating['user_id'])->where('trip_route_id', $rating['trip_route_id'])->first();
    //         if(!$booking) continue;

    //         list($ratingValue, $userRating) = $booking->calculateUserRating($rating['rate']);
    //         $booking->user_rating = $ratingValue;
    //         $booking->save();

    //         $user = $booking->user;
    //         $user->rating = $userRating;
    //         $user->save();

    //     }

    //     return $this->api->json(true, 'RATING_DONE', 'Rating done.');

    // }




    // /**
    //  * complete trip
    //  * if user rating done all 
    //  * if all trip routes status trip_ended
    //  */
    // public function completeTrip(Request $request)
    // {
    //     $trip = $this->trip
    //     ->where('driver_id', $request->auth_driver->id)
    //     ->where("id", $request->trip_id)
    //     ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED])
    //     ->first();

    //     //check all trip routes status trip_ended
    //     if($trip->tripRoutes->where('status', TripModel::TRIP_ENDED)->count() != $trip->tripRoutes->count()) {
    //         return $this->api->json(false, 'TRIP_POINTS_NOT_REACHED', 'All trip points not reached');
    //     }

    //     //check all trip user bookings rated
    //     $tripRouteIds = $trip->tripRoutes->pluck('id')->all();
    //     $bookings = $this->userTrip->where('trip_id', $trip->id)->whereIn('trip_route_id', $tripRouteIds)->get();

    //     if($bookings->count() != $bookings->where('user_rating', '<>', 0)->count()) {
    //         return $this->api->json(false, 'TRIP_BOOINKG_RATINGS_NOT_DONE', 'All the user bookings not rated');
    //     }


    //     $trip->status = TripModel::COMPLETED;
    //     $trip->save();
        
    //     $driver = $request->auth_driver;
    //     $driver->is_available = 1;
    //     $driver->save();

    //     return $this->api->json(true, 'TRIP_COMPLETED', 'Trip completed');


    // }





    // /**
    //  * return trip histories
    //  */
    // public function getTripHistories(Request $request)
    // {

    //     $trips = $this->trip
    //     ->with('tripPoints', 'tripRoutes', 'tripRoutes.userBookings', 'tripRoutes.userBookings.user')
    //     ->where('driver_id', $request->auth_driver->id)
    //     ->orderBy('date_time', 'desc')
    //     ->where(function($query){
    //         $query->where('status', TripModel::COMPLETED)
    //         ->orWhere('status', TripModel::TRIP_CANCELED);
    //     })
    //     ->paginate(2);


    //     $trips->map(function($trip){
            
    //         if($trip->invoice) {
    //             $trip->invoice['map_url'] = $trip->invoice->getStaticMapUrl();
    //         }
            
    //     });

    //     return $this->api->json(true, 'TRIP_HISTORIES', 'Trip histories', [
    //         'trip_histories'=> $trips->items(),
    //         'paging' => [
    //             'total' => $trips->total(),
    //             'has_more' => $trips->hasMorePages(),
    //             'next_page_url' => $trips->nextPageUrl()?:'',
    //             'count' => $trips->count(),
    //         ]
    //     ]);
    // }



    // /**
    //  * return all admin trip routes
    //  */
    // public function getAllSourceDetinationPoints(Request $request)
    // {
    //     $spoints = $this->routePoint->where('tag', 'SOURCE')->groupBy('address')->get();
    //     $dpoints = $this->routePoint->where('tag', 'DESTINATION')->groupBy('address')->get();
    //     $ipoints = $this->routePoint->where('tag', 'INTERMEDIATE')->groupBy('address')->get();

    //     return $this->api->json(true, 'S_D_POINTS', 'Source & destination points', [
    //         's_points' => $spoints,
    //         'd_points' => $dpoints,
    //         'i_points' => $ipoints,
    //     ]);

    // }



}
