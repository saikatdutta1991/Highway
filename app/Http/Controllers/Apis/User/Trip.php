<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use App\Repositories\Email;
use App\Repositories\Gateway;
use Illuminate\Http\Request;
use App\Models\RideRequestInvoice as Invoice;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\Trip\Trip as TripModel;
use App\Models\Trip\TripBooking;

/* 
;
use App\Repositories\SocketIOClient;
use App\Models\TripPoint;
use App\Models\TripRoute;

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
        $this->booking = app('App\Models\Trip\TripBooking');
        $this->invoice = app('App\Models\RideRequestInvoice');
        $this->setting = app('App\Models\Setting');
        $this->transaction = app('App\Models\Transaction');
        $this->email = app('App\Repositories\Email');
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

        $trips = $trips->with('adminRoute', 'points')->get();
        
        return $this->api->json(true, 'TRIPS', 'Trips', [
            'count' => $trips->count(),
            'trips' => $trips
        ]);



    }





    /**
     * book trip by trip id
     */
    public function bookTrip(Request $request)
    {        

        /**find trip by given id */
        $trip = $this->trip->where('id', $request->trip_id)
        ->where('status', TripModel::CREATED)
        ->first();
        
        /** if no trip found */
        if(!$trip) {
            return $this->api->json(false, "INVALID_TRIP", 'You are not allowed to book this trip');
        }

        /**validate seats */
        if(intval($request->seats) < 1 || intval($request->seats) > $trip->seats_available) {
            return $this->api->json(false, 'NO_SEATS_AVAILABLE', "No seats available for this trip");
        }

        /**validate source and destination point id*/
        $sourcePoint = $trip->points->where('id', $request->source_point)->where('tag', 'SOURCE')->first();
        $destPoint = $trip->points->where('id', $request->destination_point)->where('tag', 'DESTINATION')->first();
        if(!$sourcePoint || !$destPoint) {
            return $this->api->json(false, 'INVALID_SOUCE_DEST', "Invalid source or destination");
        }

        
       /*  dd('success'); */

        try {

            DB::beginTransaction();
            
            /** creating booking */
            $booking = new $this->booking;
            $booking->user_id = $request->auth_user->id;
            $booking->trip_id = $trip->id;
            $booking->boarding_point_id = $request->source_point;
            $booking->dest_point_id = $request->destination_point;
            $booking->booked_seats = $request->seats;
            $booking->booking_status = TripBooking::INITIATED;
            $booking->payment_mode = TripModel::ONLINE;
            $booking->payment_status = TripModel::NOT_PAID;

            
            /**creating invoice */
            $invoice = new $this->invoice;
            $invoice->invoice_reference = $this->invoice->generateInvoiceReference();
            $invoice->payment_mode = $booking->payment_mode;
            
            //calculate fare with multiple with no of seats
            $invoice->ride_fare = $this->utill->formatAmountDecimalTwo($trip->adminRoute->base_fare * $booking->booked_seats);
            $invoice->access_fee = $this->utill->formatAmountDecimalTwo($trip->adminRoute->access_fee * $booking->booked_seats);
            $invoice->tax = $this->utill->formatAmountDecimalTwo($trip->adminRoute->tax_fee * $booking->booked_seats);
            $invoice->total = $invoice->ride_fare + $invoice->access_fee + $invoice->tax;
            $invoice->currency_type = $this->setting->get('currency_code');

            list($invoiceImagePath, $invoiceImageName) = $invoice->saveGoogleStaticMap($sourcePoint->latitude, $sourcePoint->longitude, $destPoint->latitutde, $destPoint->longitude);
            $invoice->invoice_map_image_path = $invoiceImagePath;
            $invoice->invoice_map_image_name = $invoiceImageName;

            $invoice->save();
            /** end creating invoice */

            
            $booking->invoice_id = $invoice->id;
            $booking->save();

            
            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('BOOK_TRIP_ERROR', $e->getMessage());
            return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }

        $booking->invoice;

        return $this->api->json(true, 'TRIP_BOOKED', 'Trip booked', [
            'booking' => $booking
        ]);

    }



     /**
     * razorpay initiate
     */
    public function initRazorpay(Request $request)
    {
        $booking = $this->booking->where('user_id', $request->auth_user->id)
        ->where('id', $request->booking_id)
        ->where('booking_status', TripBooking::INITIATED)
        ->where('payment_status', TripModel::NOT_PAID)
        ->where('payment_mode', TripModel::ONLINE)
        ->with('invoice')
        ->first();

        if(!$booking) {
            return $this->api->json(false, 'INVALID_BOOKING_ID', 'Invalid booking id');
        }

        try {

            $razorpay = Gateway::instance('razorpay');
            $order = $razorpay->initiate($booking->invoice->invoice_reference, $booking->invoice->total * 100);

        } catch(\Exception $e) {
           $this->api->log('RAZORPAY_INIT_ERROR', $e->getMessage());
           return $this->api->unknownErrResponse();
        }
        

        return $this->api->json(true, 'RAZORPAY_INITIATED', 'Razorpay initiated', [
            'order_id' => $order->id,
            'razorpay_api_key' => $razorpay->publickeys()['RAZORPAY_API_KEY']
        ]);

    }


    /**
     * make razorpay payment
     */
    public function makeRazorpayPayment(Request $request)
    {

        $booking = $this->booking->where('user_id', $request->auth_user->id)
        ->where('id', $request->booking_id)
        ->where('booking_status', TripBooking::INITIATED)
        ->where('payment_status', TripModel::NOT_PAID)
        ->where('payment_mode', TripModel::ONLINE)
        ->with('invoice')
        ->first();

        /** if booking id invalid */
        if(!$booking) {
            return $this->api->json(false, 'INVALID_BOOKING_ID', 'Invalid booking id');
        }

        $razorpay = Gateway::instance('razorpay');
        $data = $razorpay->charge($request);

        if(false === $data) {
            return $this->api->json(false, 'UNKOWN_ERROR', 'Unknown error. Try again or contact to service provider');
        }

        //check order receipt and invoice referecne same or not
        $orderReceipt = isset($data['extra']['order']['receipt']) ? $data['extra']['order']['receipt'] : '';
        if($orderReceipt != $booking->invoice->invoice_reference) {
            return $this->api->json(false, 'UNKOWN_ERROR', 'Unknown error. Try again or contact to service provider');
        }


        try {
            DB::beginTransaction();

            $booking->payment_status = TripModel::PAID;
            $booking->booking_status = TripBooking::BOOKING_CONFIRMED;
            $booking->save();

            $transaction = new $this->transaction;
            $transaction->trans_id = $data['transaction_id'];
            $transaction->amount = $data['amount'];
            $transaction->currency_type = $data['currency_type'];
            $transaction->gateway = $razorpay->gatewayName();   
            $transaction->extra_info = json_encode($data['extra']);
            $transaction->status = $data['status'];  
            $transaction->payment_method = $data['method'];
            $transaction->save();


            $trip = $booking->trip;
            $trip->seats_available -= $booking->booked_seats;
            $trip->save();


            $invoice = $booking->invoice;
            $invoice->transaction_table_id = $transaction->id;
            $invoice->payment_status = TripModel::PAID;
            $invoice->save();
            
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('RAZORPAY_CHARGE_ERROR', $e);
            $this->api->log('RAZORPAY_CHARGE_ERROR', ['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return $this->api->json(false, 'UNKOWN_ERROR', 'Unknown error. Try again or contact to service provider');
        }


        /** send notifications to user via email, sms, push */
        $user = $request->auth_user;
        $currencySymbol = $this->setting->get('currency_symbol');
        $msgTxt = "Trip {$booking->trip->name} booking confirmed. Payment {$currencySymbol}{$invoice->total} has been made successfully. Check email for more details.";
        $user->sendPushNotification("Booking Confirmed", $msgTxt);
        $user->sendSms($msgTxt);
        $this->email->sendUserTripInvoiceEmail($booking);
       

       
        /** send notifications to driver */
        $driver = $booking->trip->driver;
        $msgTxt = "One new booking made for trip {$booking->trip->name}";
        $driver->sendPushNotification("{$booking->trip->name} trip has been booked", $msgTxt);
        $driver->sendSms($msgTxt);
    

        return $this->api->json(true, 'PAID', 'Payment successful');

    }





    /**
     * returns booked trips
     */
    public function getBookedTrips(Request $request)
    {

        $bookings = $this->booking->where('user_id', $request->auth_user->id)
        ->orderBy('created_at')
        ->with('trip', 'trip.driver', 'invoice', 'boardingPoint', 'destPoint')
        ->paginate(100);

        /** adding new data eg: invoice map url */
        $bookings->map(function($booking){
            if(!$booking->invoice) return;
            $booking->invoice['map_url'] = $booking->invoice->getStaticMapUrl();
            $booking->trip->driver['profile_photo_url'] = $booking->trip->driver->profilePhotoUrl();
        });


        return $this->api->json(true, 'BOOKED_TRIPS', 'Booked trips', [
            'bookings' => $bookings->items(),
            'paging' => [
                'total' => $bookings->total(),
                'has_more' => $bookings->hasMorePages(),
                'next_page_url' => $bookings->nextPageUrl()?:'',
                'count' => $bookings->count(),
            ]
        ]);

     
        

        


    }




   



    






    /**
     * give rating to trip driver
     */
    public function rateTripDriver(Request $request)
    {

        $booking = $this->booking->where('user_id', $request->auth_user->id)
        ->where('id', $request->booking_id)
        ->where('driver_rating', 0)
        ->first();

        
        if(!$booking || !in_array($request->rating, TripBooking::RATINGS)) {
            return $this->api->json(false, 'INVALID_REQUEST', 'Invalid Request, Try again.');
        }


        list($ratingValue, $driverRating) = $booking->calculateDriverRating($request->rating);
        
        $booking->driver_rating = $ratingValue;
        $booking->save();

        $driver = $booking->trip->driver;
        $driver->rating = $driverRating;
        $driver->save();

        return $this->api->json(true, 'RATING_DONE', 'Rating done');

    }


}
