<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HirePackage;
use Validator;
use App\Repositories\Utill;
use Carbon\Carbon;
use App\Models\DriverBooking;
use App\Jobs\ProcessDriverRating;
use App\Models\Coupons\Coupon;
use App\Repositories\Gateway;
use App\Models\Transaction;
use App\Models\Setting;
use DB;

class Hiring extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }


    /** 
     * returns all hiring pacakges
     */
    public function getHiringPackages()
    {
        $packages = HirePackage::orderBy('created_at', "desc")->get();
        return $this->api->json(true, "PACKAGES", "Packages fetched", [ "packages" => $packages ]);
    }


    /** create new hiring request */
    public function createRequest(Request $request)
    {
        $packageids = HirePackage::select('id')->get()->pluck('id')->toArray();
        list($latRegex, $longRegex) = Utill::regexLatLongValidate();

        $validator = Validator::make($request->all(), [
            "package_id" => "required|in:" . implode(",", $packageids),
            'pickup_address' => 'required|min:1|max:256', 
            'pickup_latitude' => ['required', 'regex:'.$latRegex], 
            'pickup_longitude' => ['required', 'regex:'.$longRegex], 
            'datetime' => ['required', 'date_format:Y-m-d H:i:s', function ($attribute, $value, $fail) {
                
                $datetime = Carbon::createFromFormat('Y-m-d H:i:s', $value, 'Asia/Kolkata')->setTimezone('UTC');
                $maxtime = Carbon::now()->addMinutes(15);
                if($datetime <= $maxtime) {
                    return $fail($attribute.' should be more than 15 minutes than current time');
                }
            }], 
            'payment_mode' => "required|in:CASH,ONLINE",
            "car_transmission" => "required|in:manual,automatic",
            "car_type" => "required|in:Hatchback,Sedan,SUV,Luxury Car", 
            "is_outstation" => "required|boolean"
            // "coupon_code" => "sometimes|required"
        ]);

        if($validator->fails()) {
            $messages = $validator->errors()->getMessages();
            $message = $messages[ key($messages) ][0];
            return $this->api->json(false, 'VALIDATION_ERROR', $message);
        }


        /** validate coupon code if exists */
        if($request->coupon_code) { 
            $couponCheck = Coupon::isValid($request->coupon_code, $request->auth_user->id, $coupon, 3);
            if( $couponCheck !== true ) {
                return $this->api->json(false, $couponCheck["errcode"], $couponCheck["errmessage"]);
            }
        }




        /** create new record */
        $booking = new DriverBooking;
        $booking->user_id = $request->auth_user->id;
        $booking->driver_id = 0;
        $booking->package_id = $request->package_id;
        $booking->pickup_address = $request->pickup_address;
        $booking->pickup_latitude = $request->pickup_latitude;
        $booking->pickup_longitude = $request->pickup_longitude;
        $booking->datetime = Carbon::createFromFormat('Y-m-d H:i:s', $request->datetime, $request->auth_user->timezone)->setTimezone('UTC');
        $booking->status = 'pending';
        $booking->payment_mode = $request->payment_mode;
        $booking->payment_status = DriverBooking::NOT_PAID;
        $booking->start_otp = rand(1000, 9999);
        $booking->car_transmission = $request->car_transmission == "manual" ? '10' : "01";
        $booking->car_type = $request->car_type;
        $booking->is_outstation = $request->is_outstation;
        $booking->coupon_code = $request->coupon_code ?: '';
        $booking->save();

        return $this->api->json(true, "BOOKING_CREATED", "Your booking created successfully.");
    }



    /** get bookings */
    public function getBookings(Request $request)
    {
        $bookings = DriverBooking::where('user_id', $request->auth_user->id)
            ->with(["driver", "package", "invoice"]);

        if($request->booking_id) {
            $bookings = $bookings->where("id", $request->booking_id);
        }

        $bookings = $bookings->orderByRaw("FIELD(status , 'pending', 'waiting_for_drivers_to_accept', 'driver_started', 'trip_started', 'driver_assigned', 'trip_ended') ASC")
            ->orderBy("datetime", "desc")
            ->get();
        
        return $this->api->json(true, "BOOKINGS", "Bookings fetched", [ "bookings" => $bookings ]);
    }


    /** rate driver */
    public function rateDriver(Request $request)
    {
        /** fetch booking from db, if not found return error */
        $booking = DriverBooking::where("status", "trip_ended")
            ->where("user_id", $request->auth_user->id)
            ->where("driver_rating", 0)
            ->where("payment_status", "PAID")
            ->where("id", $request->booking_id)
            ->first();

        if(!$booking) {
            return $this->api->json(true, "FAILED", "Invalid booking id to rate user.");
        }


        //validate rating number
        if(!$request->has('rating') || !in_array($request->rating, DriverBooking::RATINGS)) {
            return $this->api->json(false, 'INVALID_RATING', 'You must have give rating within '.implode(',', DriverBooking::RATINGS));
        }

        //saving ride request rating
        $booking->driver_rating = $request->rating;
        $booking->save();


        /** push calcualte driver to job */
        ProcessDriverRating::dispatch($booking->driver_id);

        return $this->api->json(true, 'RATED', 'Driver rated successfully.');
    }




    /**
     * razorpay initiate
     */
    public function initRazorpay(Request $request)
    {
        $booking = DriverBooking::where('user_id', $request->auth_user->id)
        ->whereIn('status', [ "trip_ended" ])
        ->where('payment_status', "NOT_PAID")
        ->where('payment_mode', "ONLINE")
        ->where('id', $request->booking_id)
        ->with(['invoice'])
        ->first();


        if(!$booking) {
            return $this->api->json(false, 'INVALID_BOOKING_ID', "Invalid booking id");
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
        $booking = DriverBooking::where('user_id', $request->auth_user->id)
        ->whereIn('status', [ "trip_ended" ])
        ->where('payment_status', "NOT_PAID")
        ->where('payment_mode', "ONLINE")
        ->where('id', $request->booking_id)
        ->with(['invoice'])
        ->first();

        if(!$booking) {
            return $this->api->json(false, 'INVALID_BOOKING_ID', "Invalid booking id");
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


        try{
            DB::beginTransaction();

            $booking->payment_status = "PAID";
            $booking->save();

            $transaction = new Transaction;
            $transaction->trans_id = $data['transaction_id'];
            $transaction->amount = $data['amount'];
            $transaction->currency_type = $data['currency_type'];
            $transaction->gateway = $razorpay->gatewayName();   
            $transaction->extra_info = json_encode($data['extra']);
            $transaction->status = $data['status'];  
            $transaction->payment_method = $data['method'];
            $transaction->save();


            $invoice = $booking->invoice;
            $invoice->transaction_table_id = $transaction->id;
            $invoice->payment_status = "PAID";
            $invoice->save();
            
            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('RAZORPAY_CHARGE_ERROR', $e);
            return $this->api->json(false, 'UNKOWN_ERROR', 'Unknown error. Try again or contact to service provider');
        }


        /**
         * send push notification to user
         */
        $user = $request->auth_user;
        $currencySymbol = Setting::get('currency_symbol');
        $user->sendPushNotification("Payment successful", "{$currencySymbol}{$invoice->total} has been paid successfully");
        $user->sendSms("{$currencySymbol}{$invoice->total} has been paid successfully");


        /** send push to driver */
        $booking->driver->sendPushNotification("User Paid", "User has paid {$currencySymbol}{$invoice->total} through online");


        return $this->api->json(true, 'PAID', 'Payment successful');

    }



}
