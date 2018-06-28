<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip\Trip as TripModel;

/* use DB;
use App\Repositories\Email;
use App\Repositories\Gateway;

use App\Models\Setting;
use App\Repositories\SocketIOClient;
use App\Models\Trip as TripModel;
use App\Models\TripPoint;
use App\Models\TripRoute;
use App\Models\Transaction;
use App\Repositories\Utill;
use App\Models\UserTrip;
use Validator; */

class Trip extends Controller
{

    /**
     * init dependencies
     */
    public function __construct()
    {
        $this->api = app('App\Repositories\Api');
        $this->trip = app('App\Models\Trip\Trip');
        $this->utill = app('UtillRepo');
    }



    /**
     * show all possible source and destination list
     * from driver created routes
     */
    public function getSourceDestList()
    {

        $sourceList = $this->trip->select('from')->groupBy('from')->get()->pluck('from')->toArray();
        $destList = $this->trip->select('to')->groupBy('to')->get()->pluck('to')->toArray();
        
        return $this->api->json(true, 'SOURCE_DEST_LIST', 'Source and destination list', [
            'source' => $sourceList,
            'dest' => $destList
        ]);
    }


    /**
     * search trips
     */
    public function searchTrips(Request $request)
    {
        $source = $request->source;
        $dest = $request->destination;

        $trips = $this->trip->where(function($query) use($source, $dest){
            $query->where('from', $source)
            ->where('to', $dest);
        })
        ->where('status', TripModel::CREATED);

        $dateRange = $this->utill->utcDateRange($request->date, $request->auth_user->timezone);
        $this->api->log('USER_TRIP_SEARCH_DATERANGE', $dateRange);
        
        /** if date param passed then search for specific date or return all beyond current datetime */
        $trips = is_array($dateRange) 
        ? $trips->whereBetween("trip_datetime", $dateRange) 
        : $trips = $trips->where("trip_datetime", ">=", date('Y-m-d H:i:s'));

        $trips = $trips->get();

        return $this->api->json(true, 'TRIPS', 'Trips', [
            'count' => $trips->count(),
            'trips' => $trips
        ]);



    }





    // /**
    //  * book trip by trip id and point id too
    //  */
    // public function bookTrip(Request $request)
    // {
        
    //     $paymentMode = in_array($request->payment_mode, TripModel::PAYMENT_MODES) ? $request->payment_mode : TripModel::CASH;

    //     //find trip point by id
    //     $tripRoute = $this->tripRoute->where('trip_id', $request->trip_id)
    //     ->where('id', $request->trip_route_id)
    //     ->whereNotIn('status', [TripModel::COMPLETED, TripModel::TRIP_STARTED, TripModel::TRIP_CANCELED])
    //     ->first();
        
    //     if(!$tripRoute) {
    //         return $this->api->json(false, "INVALID", 'You are not allowed to book this trip');
    //     }

    //     //if seats not not available
    //     $seats = ($request->no_of_seats == '') ? 1 : $request->no_of_seats; //if no of seats null then make 1
    //     if($tripRoute->seats_available == 0 || $tripRoute->seats_available < $seats) {
    //         return $this->api->json(false, 'NO_SEATS_AVAILABLE', "No seats available for this trip");
    //     }

    //     //check if user has already booked this trip alredy or not
    //     if($this->userTrip->where('trip_id', $tripRoute->trip_id)->where('trip_route_id', $tripRoute->id)->exists()) {
    //         return $this->api->json(false, 'ALREADY_BOOKED', 'You have already booked this trip');
    //     }


    //     try {

    //         DB::beginTransaction();
            
    //         //making trip point status to booked          
    //         $tripRoute->status = TripModel::BOOKED;
    //         $tripRoute->save();

    //         //making trip status booked
    //         $trip = $tripRoute->trip;
    //         $trip->status = TripModel::BOOKED;          
    //         $trip->save();

    //         //inserting user trip record
    //         $userTrip = new $this->userTrip;
    //         $userTrip->user_id = $request->auth_user->id;
    //         $userTrip->trip_id = $trip->id;
    //         $userTrip->trip_route_id = $tripRoute->id;
    //         $userTrip->no_of_seats_booked = $seats;
    //         $userTrip->status = TripModel::BOOKED;
    //         $userTrip->payment_mode = $paymentMode;
    //         $userTrip->payment_status = TripModel::NOT_PAID;
    //         $userTrip->trip_invoice_id = 0;
    //         $userTrip->user_rating = 0;
    //         $userTrip->driver_rating = 0;
            
    //         $userTrip->save();


    //         /**
    //          * change trip routes avaialbe seats
    //          */
    //         $seatAffects = explode(",", $tripRoute->seat_affects);
    //         foreach($seatAffects as $tripRouteId) {
    //             $tr = $this->tripRoute->find($tripRouteId);
    //             $tr->seats_available -= $seats;
    //             $tr->save();

    //             //changing trip route setas availabe current obeject so that does not make any confusion
    //             if($tr->id == $tripRoute->id) {
    //                 $tripRoute->seats_available = $tr->seats_available;
    //             }

    //         }


            
    //         DB::commit();

    //     } catch(\Exception $e) {
    //         DB::rollback();
    //         $this->api->log('BOOK_TRIP_ERROR', $e->getMessage());
    //         return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
    //     }


    //     //send push notification and sms to driver
    //     $driver = $trip->driver;
    //     $user = $request->auth_user;
    //     $msgBody = "{$user->fullname()} has booked trip({$trip->name}) from {$tripRoute->start_point_address}";
    //     $driver->sendPushNotification($trip->name.' trip has been booked', $msgBody);
    //     $driver->sendSms($msgBody);
        
    //     //send sms to user and email
    //     $user->sendSms("Your trip has been booked. From : {$tripRoute->start_point_address} | To : {$tripRoute->end_point_address} on {$tripRoute->trip->tripFormatedDateString()} at {$tripRoute->trip->tripFormatedTimeString()}");

    //     return $this->api->json(true, 'TRIP_BOOKED', 'Trip booked', [
    //         'user_trip' => $userTrip
    //     ]);


    // }






    // /**
    //  * returns booked trips those are not canceled or user canceled
    //  */
    // public function getBookedTrips(Request $request)
    // {
    //     $trips = $this->userTrip->where('user_id', $request->auth_user->id)
    //     ->whereNotIn('status', [UserTrip::USER_CANCELED, TripModel::TRIP_CANCELED, TripModel::COMPLETED])
    //     ->with('trip', 'tripRoute', 'invoice', 'trip.driver')
    //     ->get();

    //     $trips->map(function($trip){
            
    //         if($trip->invoice) {
    //             $trip->invoice['map_url'] = $trip->invoice->getStaticMapUrl();
    //         }
            
    //         $trip->trip->driver['profile_photo_url'] = $trip->trip->driver->profilePhotoUrl();
    //     });


    //     /**
    //      * find current running trip
    //      */
    //      $currentTrip = $this->userTrip
    //     ->where('user_id', $request->auth_user->id)
    //     ->whereNotIn('status', [TripModel::COMPLETED, TripModel::BOOKED, TripModel::DRIVER_STARTED, TripModel::TRIP_CANCELED, TripModel::INITIATED])
    //     ->with('trip', 'tripRoute', 'invoice', 'trip.driver')
    //     ->first();

    //     if(!$currentTrip) {
    //         //check if any ride completd by not driver rated
    //         $currentTrip = $this->userTrip
    //         ->where('user_id', $request->auth_user->id)
    //         ->with('trip', 'tripRoute', 'invoice', 'trip.driver')
    //         ->where('status', TripModel::COMPLETED)
    //         ->where('driver_rating', 0)
    //         ->first();
    //     }

        
    //     if($currentTrip && $currentTrip->invoice) {
    //         $currentTrip->invoice['map_url'] = $currentTrip->invoice->getStaticMapUrl();
    //     }




    //     return $this->api->json(true, 'BOOKED_TRIPS', 'Booked trips', [
    //         'trips' => $trips,
    //         'current_trip' => $currentTrip
    //     ]);


    // }




    // /**
    //  * razorpay initiate
    //  */
    // public function initRazorpay(Request $request)
    // {
    //     $userBooking = $this->userTrip->where('user_id', $request->auth_user->id)
    //     ->whereIn('status', [TripModel::TRIP_ENDED])
    //     ->where('payment_status', TripModel::NOT_PAID)
    //     ->where('payment_mode', TripModel::ONLINE)
    //     ->where('id', $request->trip_booking_id)
    //     ->with(['invoice'])
    //     ->first();


    //     if(!$userBooking) {
    //         return $this->api->json(false, 'INVALID_BOOKING_ID', 'Invalid booking id');
    //     }

    //     try {

    //         $razorpay = Gateway::instance('razorpay');
    //         $order = $razorpay->initiate($userBooking->invoice->invoice_reference, $userBooking->invoice->total * 100);

    //     } catch(\Exception $e) {
    //        $this->api->log('RAZORPAY_INIT_ERROR', $e->getMessage());
    //        return $this->api->unknownErrResponse();
    //     }
        

    //     return $this->api->json(true, 'RAZORPAY_INITIATED', 'Razorpay initiated', [
    //         'order_id' => $order->id,
    //         'razorpay_api_key' => $razorpay->publickeys()['RAZORPAY_API_KEY']
    //     ]);

    // }



    // /**
    //  * make razorpay payment
    //  */
    // public function makeRazorpayPayment(Request $request)
    // {
    //     $userBooking = $this->userTrip->where('user_id', $request->auth_user->id)
    //     ->whereIn('status', [TripModel::TRIP_ENDED])
    //     ->where('payment_status', TripModel::NOT_PAID)
    //     ->where('payment_mode', TripModel::ONLINE)
    //     ->where('id', $request->trip_booking_id)
    //     ->with(['invoice'])
    //     ->first();

    //     if(!$userBooking) {
    //         return $this->api->json(false, 'INVALID_BOOKING_ID', 'Invalid booking id');
    //     }

    //     $razorpay = Gateway::instance('razorpay');
    //     $data = $razorpay->charge($request);

    //     if(false === $data) {
    //         return $this->api->json(false, 'UNKOWN_ERROR', 'Unknown error. Try again or contact to service provider');
    //     }

    //     //check order receipt and invoice referecne same or not
    //     $orderReceipt = isset($data['extra']['order']['receipt']) ? $data['extra']['order']['receipt'] : '';
    //     if($orderReceipt != $userBooking->invoice->invoice_reference) {
    //         return $this->api->json(false, 'UNKOWN_ERROR', 'Unknown error. Try again or contact to service provider');
    //     }


    //     try{
    //         DB::beginTransaction();

    //         $userBooking->payment_status = TripModel::PAID;
    //         $userBooking->status = TripModel::COMPLETED;
    //         $userBooking->save();

    //         $transaction = new $this->transaction;
    //         $transaction->trans_id = $data['transaction_id'];
    //         $transaction->amount = $data['amount'];
    //         $transaction->currency_type = $data['currency_type'];
    //         $transaction->gateway = $razorpay->gatewayName();   
    //         $transaction->extra_info = json_encode($data['extra']);
    //         $transaction->status = $data['status'];  
    //         $transaction->payment_method = $data['method'];
    //         $transaction->save();


    //         $invoice = $userBooking->invoice;
    //         $invoice->transaction_table_id = $transaction->id;
    //         $invoice->payment_status = TripModel::PAID;
    //         $invoice->save();
            
    //         DB::commit();
    //     } catch(\Exception $e) {
    //         DB::rollback();
    //         $this->api->log('RAZORPAY_CHARGE_ERROR', $e);
    //         return $this->api->json(false, 'UNKOWN_ERROR', 'Unknown error. Try again or contact to service provider');
    //     }


    //     //send invoice via email
    //     $this->email->sendUserTripInvoiceEmail($userBooking);
       

    //     /**
    //      * send push notification to user
    //      */
    //     $user = $request->auth_user;
    //     $currencySymbol = $this->setting->get('currency_symbol');
    //     $user->sendPushNotification("Payment successful", "{$currencySymbol}{$invoice->total} has been paid successfully");
    //     $user->sendSms("{$currencySymbol}{$invoice->total} has been paid successfully");

    //     return $this->api->json(true, 'PAID', 'Payment successful');

    // }






    // /**
    //  * give rating to trip driver
    //  */
    // public function rateTripDriver(Request $request)
    // {
    //     $userTrip = $this->userTrip
    //     ->where('user_id', $request->auth_user->id)
    //     ->where('trip_id', $request->trip_id)
    //     ->where('trip_route_id', $request->trip_route_id)
    //     ->where(function($query){
    //         $query->where('status', TripModel::TRIP_ENDED)->orWhere('status', TripModel::COMPLETED);
    //     })
    //     ->first();
        

    //     if(!$userTrip) {
    //         return $this->api->json(false, 'INVALID_REQUEST', 'Invalid Request, Try again.');
    //     }


    //     list($ratingValue, $driverRating) = $userTrip->calculateDriverRating($request->rating);
        
    //     $userTrip->driver_rating = $ratingValue;
    //     $userTrip->save();

    //     $driver = $userTrip->trip->driver;
    //     $driver->rating = $driverRating;
    //     $driver->save();

    //     return $this->api->json(true, 'RATING_DONE', 'Rating done');

    // }



    // /**
    //  * send user trip histories completed
    //  */
    // public function getHistories(Request $request)
    // {
    //     $userTrips = $this->userTrip->where('user_id', $request->auth_user->id)
    //     ->where(function($query){
    //         $query->where('status', TripModel::TRIP_CANCELED)
    //         ->orWhere('status', TripModel::COMPLETED)
    //         ->orWhere('status', UserTrip::USER_CANCELED);
    //     })
    //     ->orderBy('created_at', 'desc')
    //     ->with('trip', 'tripRoute', 'invoice', 'trip.driver')
    //     ->paginate(100);

    //     $userTrips->map(function($userTrip){
            
    //         if($userTrip->invoice) {
    //             $userTrip->invoice['map_url'] = $userTrip->invoice->getStaticMapUrl();
    //         }

    //         $userTrip->trip->driver['profile_photo_url'] = $userTrip->trip->driver->profilePhotoUrl();
    //     });

    //     return $this->api->json(true, 'TRIP_HISTORIES', 'trip histories', [
    //         'user_trips'=> $userTrips->items(),
    //         'paging' => [
    //             'total' => $userTrips->total(),
    //             'has_more' => $userTrips->hasMorePages(),
    //             'next_page_url' => $userTrips->nextPageUrl()?:'',
    //             'count' => $userTrips->count(),
    //         ]
    //     ]);
   

    // }





       



}
