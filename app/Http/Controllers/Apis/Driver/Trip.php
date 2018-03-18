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
use App\Models\TripRoute;
use App\Repositories\Utill;
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
    public function __construct(
        Transaction $transaction,
        UserTrip $userTrip, 
        TripRoute $tripRoute,
        RideFare $rideFare, 
        VehicleType $vehicleType, 
        TripPoint $tripPoint, 
        TripModel $trip, 
        Utill $utill, 
        Setting $setting, 
        Email $email, 
        Api $api, 
        SocketIOClient $socketIOClient,
        RideInvoice $rideInvoice
    )
    {
        $this->transaction = $transaction;
        $this->userTrip = $userTrip;
        $this->tripRoute = $tripRoute;
        $this->rideFare = $rideFare;
        $this->vehicleType = $vehicleType;
        $this->tripPoint = $tripPoint;
        $this->trip = $trip;
        $this->utill = $utill;
        $this->setting = $setting;
        $this->email = $email;
        $this->api = $api;
        $this->socketIOClient = $socketIOClient;
        $this->rideInvoice = $rideInvoice;
    }


    /**
     * create trip for driver 
     * create points, point orders
     * calculate affected routes
     * use algoright for consecutive sub array
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

        
        /**
         * get ride fare for further ride calculation
         */
        $vTypeId = $this->vehicleType->getIdByCode($request->auth_driver->vehicle_type);
        $rideFare = $this->rideFare->where('vehicle_type_id', $vTypeId)->first();

        $trip = new $this->trip;
        $trip->driver_id = $request->auth_driver->id;
        $trip->name = ucfirst(trim($request->trip_name));
        $trip->no_of_seats = $request->seats;
        $trip->date_time = $this->utill->timestampStringToUTC($request->trip_date_time, $request->auth_driver->timezone)->toDateTimeString();
        $trip->status = TripModel::INITIATED;

        try {

            DB::beginTransaction();
            
            $trip->save();

            //saving trip points
            $tripPoints = [];
            /* $tripRoute */
            $order = 1;
            foreach($request->points as $point) {
                $tripPoint = new $this->tripPoint;
                $tripPoint->trip_id = $trip->id;
                $tripPoint->order = $order++;
                $tripPoint->address = $point['address'];
                $tripPoint->latitude = $point['latitude'];
                $tripPoint->longitude = $point['longitude'];
                $tripPoint->distance = isset($point['distance']) ? $point['distance'] / 1000 : 0;
                $tripPoint->time = isset($point['time']) ? $point['time'] : 0;
                $tripPoint->status = TripModel::INITIATED;
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
        
                    $time = 0;
                    $distance = 0;
                    for($i = $scaleStartIndex; $i < $scaleEndIndex; $i++) {
            
                        $tripPoint = $tripPoints[$i + 1];
                        $time += $tripPoint->time;
                        $distance += $tripPoint->distance;
                    }

                    /**
                     * save trip routes in database
                     */
                    //echo "{$tripPoints[$scaleStartIndex]->address} -  {$tripPoints[$scaleEndIndex]->address} $time $distance<br>";
                    $tripRoute = new $this->tripRoute;
                    $tripRoute->trip_id = $trip->id;
                    $tripRoute->start_point_address = $tripPoints[$scaleStartIndex]->address;
                    $tripRoute->start_point_latitude = $tripPoints[$scaleStartIndex]->latitude;
                    $tripRoute->start_point_longitude = $tripPoints[$scaleStartIndex]->longitude;
                    $tripRoute->start_point_order = $tripPoints[$scaleStartIndex]->order;
                    $tripRoute->end_point_address = $tripPoints[$scaleEndIndex]->address;
                    $tripRoute->end_point_latitude = $tripPoints[$scaleEndIndex]->latitude;
                    $tripRoute->end_point_longitude = $tripPoints[$scaleEndIndex]->longitude;
                    $tripRoute->end_point_order = $tripPoints[$scaleEndIndex]->order;
                    $tripRoute->seat_affects = '';
                    $tripRoute->seats_available = $trip->no_of_seats;
                    $tripRoute->estimated_distance = $distance;
                    $tripRoute->estimated_time = $time;
                    $tripRoute->status = TripModel::INITIATED;

                    //calculate fare
                    $fare = $rideFare->calculateFare($distance, $time);
                    $tripRoute->estimated_fare = $fare['total'];

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
     * delete a particular trip if only if trip status is initiated
     */
    public function deleteTrip(Request $request)
    {
        //fetching correct trip to delete
        $trip = $this->trip->where('id', $request->trip_id)
        ->where('driver_id', $request->auth_driver->id)
        ->where('status', TripModel::INITIATED)
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

        return $this->api->json(true, 'TRIP_DELETED', 'Trip deleted');
        
    }




    /**
     * get all trips those are not completed
     */
    public function getTrips(Request $request)
    {

        $trips = $this->trip
        //->with('tripPoints', 'tripRoutes', 'tripRoutes.userBookings', 'tripRoutes.userBookings.user')
        ->with('tripPoints', 'tripRoutes')
        ->where('driver_id', $request->auth_driver->id)
        ->orderBy('date_time', 'desc')
        ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED]);

        $dateRange = app('UtillRepo')->utcDateRange($request->date, $request->auth_driver->timezone);

        if(is_array($dateRange)) {
            $trips = $trips->whereBetween('date_time', $dateRange);
        }
        //else take all trips after current date 
        else{
            $trips = $trips->where('date_time', '>=', date('Y-m-d H:i:s'));
        }


        /**
         * find current running trip
         */
         $currentTrip = $this->trip
        ->where('driver_id', $request->auth_driver->id)
        ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED, TripModel::INITIATED])
        ->first();


        $trips = $trips->get();

        return $this->api->json(true, 'TRIPS', 'Your trips', [
            'trips' => $trips,
            'current_trip' => $currentTrip
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
        ->first();

        if(!$trip) {
            return $this->api->json(false, "INVALID_TRIP_ID", 'Invalid trip id');
        }

        $tripPoints = $trip->tripPoints;
        $tripRoutes = $trip->tripRoutes;

        //find how many boarding and unboarding users for a particular point
        foreach($tripPoints as $index => $tripPoint) {

            //find how many trip routes start from specific point
            $tripRouteIds = $trip->tripRoutes->where('start_point_order', $tripPoint->order)->pluck('id')->all();
            
            //find how many user bookings made for all triprouteids
            $userTripBookings = $this->userTrip->whereIn('trip_route_id', $tripRouteIds)->with('user')->get();

            $tripPoints[$index]['boarding_user_bookings'] = $userTripBookings;
            $tripPoints[$index]['total_boarding_users_count'] = $userTripBookings->sum('no_of_seats_booked');


             //find how many trip routes end from specific point
            $tripRouteIds = $trip->tripRoutes->where('end_point_order', $tripPoint->order)->pluck('id')->all();
            
            //find how many user bookings made for all triprouteids
            $userTripBookings = $this->userTrip->whereIn('trip_route_id', $tripRouteIds)->with('user')->get();

            $tripPoints[$index]['unboarding_user_bookings'] = $userTripBookings;
            $tripPoints[$index]['total_unboarding_users_count'] = $userTripBookings->sum('no_of_seats_booked');



        }


        
        unset($trip->tripRoutes);
        unset($trip->tripPoints);
        
        return $this->api->json(true, 'TRIP', 'Trip details', [
            'trip' => $trip,
            'trip_points' => $tripPoints,
            'tirp_routes' => $tripRoutes
        ]);


    }





    /**
     * driver start a trip
     * DRIVER_STARTED
     */
    public function driverStartedTrip(Request $request)
    {
        $trip = $this->trip
        ->where('driver_id', $request->auth_driver->id)
        ->where("id", $request->trip_id)
        ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED])
        ->first();

        if(!$trip) {
            return $this->api->json(false, "INVALID_TRIP_ID", 'Invalid trip id');
        }


        try {

            DB::beginTransaction();
            
            //change trip status to driver started the trip
            $trip->status = TripModel::DRIVER_STARTED;
            $trip->save();

            //update trip_routes status to driver started the trip
            $this->tripRoute->where('trip_id', $trip->id)->update(['status' => TripModel::DRIVER_STARTED]);

            //update user bookings status to driver started the trip
            $this->userTrip->where('trip_id', $trip->id)->update(['status' => TripModel::DRIVER_STARTED]);

            //making driver un availbable
            $authDriver = $request->auth_driver;
            $authDriver->is_available = 0;
            $authDriver->save();
            
            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('DRIVER_STARTED_TRIP', $e->getMessage());
            return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }

        

        //send all user push notification that driver has started the trip
        $bookings = $this->userTrip->where('trip_id', $trip->id)->with('user')->get();
        foreach($bookings as $booking) {
            $booking->user->sendPushNotification("Trip {$trip->name} has been started", "Driver has started the trip, you will be notified as soon driver reaches your pickup point");
        }


        return $this->api->json(true, 'TRIP_DRIVER_STARTED', 'Trip started');

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
        $trip = $this->trip
        ->where('driver_id', $request->auth_driver->id)
        ->where("id", $request->trip_id)
        ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED])
        ->first();

        $tripPoint = $this->tripPoint->where('trip_id', $trip->id)->where('id', $request->trip_point_id)->first();
        $tripPoint->status = TripModel::DRIVER_REACHED;
        $tripPoint->save();

        //find how many trip routes start from specific point
        $tripRouteIds = $trip->tripRoutes->where('start_point_order', $tripPoint->order)->pluck('id')->all();
        //update driver reached for all routes whose starting point is current trip point
        $this->tripRoute->where('trip_id', $trip->id)->where('start_point_order', $tripPoint->order)->update(['status' => TripModel::DRIVER_REACHED]);
        //update alluser bookings status driver reached
        $this->userTrip->wherein('trip_route_id', $tripRouteIds)->update(['status' => TripModel::DRIVER_REACHED]);
            
        //find how many user bookings made for all triprouteids ( boarding passengers)
        $boardingUserBookings = $this->userTrip->whereIn('trip_route_id', $tripRouteIds)->with('user')->get();

        /**
         * send all boarding passenders push notification 
         * that driver has reached boarding point
         */
        foreach($boardingUserBookings as $booking) {
            $booking->user->sendPushNotification("Trip {$trip->name} : driver reached", "Driver has reached your boarding point {$tripPoint->address}, You can contact the driver.");
        }


        //find all trip routes end in specific point (current point)
        $unboaringRoutes = $trip->tripRoutes->where('end_point_order', $tripPoint->order);

        /**
         * get ride fare for further ride calculation
         */
        $vTypeId = $this->vehicleType->getIdByCode($request->auth_driver->vehicle_type);
        $rideFare = $this->rideFare->where('vehicle_type_id', $vTypeId)->first();

        foreach($unboaringRoutes as $route) {
            
            //update route trip_ended if trip_started
            $route->status = TripModel::TRIP_ENDED;
            $route->save();

            //find all unboarding user bookings
            $unboardingUserBookings = $this->userTrip->where('trip_route_id', $route->id)->with('user')->get();

            $fare = $rideFare->calculateFare($route->estimated_distance, $route->estimated_time);


            //for each user booking set invoice
            foreach($unboardingUserBookings as $booking) {

                //creting invoice
                $invoice = new $this->rideInvoice;
                $invoice->invoice_reference = $this->rideInvoice->generateInvoiceReference();
                $invoice->payment_mode = $booking->payment_mode;
                $invoice->ride_fare = $fare['ride_fare'];
                $invoice->access_fee = $fare['access_fee'];
                $invoice->tax = $fare['taxes'];
                $invoice->total = $fare['total'];
                $invoice->currency_type = $this->setting->get('currency_code');

                list($invoiceImagePath, $invoiceImageName) = $invoice->saveGoogleStaticMap($route->start_point_latitude, $route->start_point_longitude, $route->end_point_latitutde, $route->end_point_longitude);
                $invoice->invoice_map_image_path = $invoiceImagePath;
                $invoice->invoice_map_image_name = $invoiceImageName;


                 //if cash payment mode then payment_status paid
                if($booking->payment_mode == TripModel::CASH) {
                    $booking->payment_status = TripModel::PAID;
                    $booking->status = TripModel::TRIP_ENDED;
                    $invoice->payment_status = TripModel::PAID;

                    //create transaction because payment successfull here
                    $transaction = new $this->transaction;
                    $transaction->trans_id = $invoice->invoice_reference;
                    $transaction->amount = $fare['total'];
                    $transaction->currency_type = $this->setting->get('currency_code');
                    $transaction->gateway = TripModel::CASH;
                    $transaction->payment_method = TripModel::COD;
                    $transaction->status = Transaction::SUCCESS;
                    $transaction->save();

                    //add transaciton_table_id in invoice
                    $invoice->transaction_table_id = $transaction->id;

                }
                
                $invoice->save();

                $booking->trip_invoice_id = $invoice->id;
                $booking->save();

                //send invoice if paid
                /* if($booking->payment_status == Ride::PAID) {
                    $this->email->sendUserRideRequestInvoiceEmail($rideRequest);
                } */

                /**
                 * send push notification to user
                 */
                $user = $booking->user;
                $currencySymbol = $this->setting->get('currency_symbol');
                $user->sendPushNotification("Your trip ended", "We hope you enjoyed our intercity trip service. Please make payment of {$currencySymbol}".$invoice->total);
                $user->sendSms("We hope you enjoyed our intercity trip service. Please make payment of {$currencySymbol}".$invoice->total);

                /**
                 * send socket push to user
                 */
                $this->socketIOClient->sendEvent([
                    'to_ids' => $user->id,
                    'entity_type' => 'user', //socket will make it uppercase
                    'event_type' => 'trip_booking_status_changed',
                    'data' => [
                        'trip' => $trip,
                        'trip_route' => $route,
                        'booking' => $booking,
                        'invoice' => $invoice->toArray(),
                    ]
                ]);

                /**
                 * dont call invoice save method after this
                 */
                $invoice->map_url = $invoice->getStaticMapUrl();


            }


        }


        return $this->api->json(true, 'TRIP_POINT_REACHED', 'Trip point reached', [
            'message' => 'Get trip details again. Dont show this message to driver'
        ]);


    }



    /**
     * start trip point
     * send all users boarding from that point 
     * take who all passengers are boarding
     */
    public function driverStartTripPoint(Request $request)
    {
        $trip = $this->trip
        ->where('driver_id', $request->auth_driver->id)
        ->where("id", $request->trip_id)
        ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_CANCELED])
        ->first();

        
        //send notification to all users that your trip has started
        $tripPoint = $this->tripPoint->where('trip_id', $trip->id)->where('id', $request->trip_point_id)->first();
        $tripPoint->status = TripModel::TRIP_STARTED;
        $tripPoint->save();
        //find how many trip routes start from specific point
        $tripRouteIds = $trip->tripRoutes->where('start_point_order', $tripPoint->order)->pluck('id')->all();
        //update driver reached for all routes whose starting point is current trip point
        $this->tripRoute->where('trip_id', $trip->id)->where('start_point_order', $tripPoint->order)->update(['status' => TripModel::TRIP_STARTED]);
        //update all user trips status trip started
        $this->userTrip->whereIn('trip_route_id', $tripRouteIds)->update(['status' => TripModel::TRIP_STARTED]);
            
        //find how many user bookings made for all triprouteids ( boarding passengers)
        $boardingUserBookings = $this->userTrip->whereIn('trip_route_id', $tripRouteIds)->with('user')->get();

        /**
         * send all boarding passenders push notification 
         * that driver has reached boarding point
         */
        $userIds = [];
        $boardingUserIds = explode(',', $request->boarding_user_ids);
        foreach($boardingUserBookings as $booking) {
            
            if(!empty($boardingUserIds) && in_array($booking->user->id, $boardingUserIds)) {
                $booking->is_boarded = 1; 
                $booking->save();
            }
            $booking->user->sendPushNotification("Trip {$trip->name} : started", "Driver has started trip at boarding point {$tripPoint->address}, You can contact the driver.");
            $userIds[] = $booking->user->id;
        }

        /**
         * send socket push to user
         */
        $this->socketIOClient->sendEvent([
            'to_ids' => implode(',', $userIds),
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'trip_booking_status_changed',
            'data' => [
                'trip' => $trip,
                'trip_point' => $tripPoint,
            ]
        ]);


        return $this->api->json(true, 'TRIP_STARTED', 'Trip started', [
            'message' => 'Get trip details again. Dont show this message to driver'
        ]);

    }


}
