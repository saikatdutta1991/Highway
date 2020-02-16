<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverBooking;
use App\Models\DriverBookingBroadcast;
use Carbon\Carbon;
use App\Models\HirePackage;
use App\Models\Setting;
use App\Repositories\Utill;
use App\Models\RideRequestInvoice as Invoice;
use App\Models\Transaction;
use DB;
use App\Repositories\SocketIOClient;
use App\Models\User;
use App\Repositories\Email;
use App\Jobs\ProcessDriverInvoice;
use App\Jobs\ProcessUserRating;
use App\Models\Coupons\Coupon;


class Hiring extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, SocketIOClient $socketIOClient, Email $email)
    {
        $this->api = $api;
        $this->socketIOClient = $socketIOClient;
        $this->email = $email;
    }

    /** get booking requests only that not accepted yet */
    public function getRequests(Request $request)
    {
        $requests = DriverBooking::where(DriverBooking::table() . ".status", "waiting_for_drivers_to_accept")
            ->join(DriverBookingBroadcast::table(), DriverBookingBroadcast::table() . ".booking_id", "=", DriverBooking::table() . ".id")
            ->where(DriverBookingBroadcast::table() . ".driver_id", $request->auth_driver->id)
            ->where(DriverBookingBroadcast::table() . ".status", "pending")
            ->select(DriverBooking::table() . ".*")
            ->orderBy(DriverBooking::table() . ".datetime", "asc")
            ->where(DriverBooking::table() . ".datetime", ">=", Carbon::now())
            ->groupBy(DriverBooking::table() . ".id")
            ->with("package");
        
        if($request->booking_id) {
            $requests = $requests->where(DriverBooking::table() . ".id", $request->booking_id);
            $requests = $requests->first();
            return $this->api->json(true, "REQUEST", "Request", [ "request" => $requests ]);
        } else {
            $requests = $requests->get();
            return $this->api->json(true, "REQUESTS", "Requests", [ "requests" => $requests ]);
        }
    
    }



    /** accept or reject incoming driver booking request */
    public function actionRequest(Request $request)
    {
        /** validate request id correct or not */
        $booking = DriverBooking::where(DriverBooking::table() . ".status", "waiting_for_drivers_to_accept")
            ->join(DriverBookingBroadcast::table(), DriverBookingBroadcast::table() . ".booking_id", "=", DriverBooking::table() . ".id")
            ->where(DriverBookingBroadcast::table() . ".driver_id", $request->auth_driver->id)
            ->where(DriverBookingBroadcast::table() . ".status", "pending")
            ->where(DriverBooking::table() . ".id", $request->request_id)
            ->select(DriverBooking::table() . ".*")
            ->with('user')
            ->lockForUpdate()
            ->first();
        
        if(!$booking) {
            return $this->api->json(true, "ERROR", "You are not allowed to accept or reject this request.");
        }


        if($request->action == "accept") {

            DB::beginTransaction();
            try {

                // update booking record
                $booking->driver_id = $request->auth_driver->id;
                $booking->status = "driver_assigned";
                $booking->save();

                // update booking broadcaasts
                DriverBookingBroadcast::where("booking_id", $request->request_id)->update(["status" => "rejected"]);
                DriverBookingBroadcast::where("booking_id", $request->request_id)->where("driver_id", $request->auth_driver->id)->update(["status" => "accepted"]);

                DB::commit();

                /** find user and send push notification */
                $date = Carbon::parse($booking->datetime, 'UTC')->setTimezone('Asia/Kolkata')->format('d/m/Y');
                $time = Carbon::parse($booking->datetime, 'UTC')->setTimezone('Asia/Kolkata')->format('h:i A');
                $booking->user->sendPushNotification("Driver Booking Confirmed", "Your Temp Driver request on {$date} at {$time} accepted by one driver.", [], "com.capefox.cabrider.ui.activities.hireDriver.DriverPackagesActivity");

            } catch(\Exception $e) {
                DB::rollback();
                return $this->api->unknownErrResponse();
            }

        } else if($request->action == "reject") {

            DriverBookingBroadcast::where("booking_id", $request->request_id)->where("driver_id", $request->auth_driver->id)->update(["status" => "rejected"]);
            
        }

        return $this->api->json(true, "ACTION_DONE", "Action taken succesfully");
    }




    /** get bookings */
    public function getBookings(Request $request)
    {
        $bookings = DriverBooking::whereNotIn("status", ["pending", "waiting_for_drivers_to_accept", "driver_assigned"])
            ->where("driver_id", $request->auth_driver->id)
            ->with("user", "package", "invoice")
            ->orderByRaw("FIELD(status , 'driver_started', 'trip_started', 'driver_assigned', 'trip_ended') ASC")
            ->orderBy("datetime", "desc")
            ->orWhere(function ($query) use( $request ) {
                return $query->where( "status", "driver_assigned" )
                ->where("driver_id", $request->auth_driver->id)
                ->where(DriverBooking::table() . ".datetime", ">=", Carbon::now());
            });


        if($request->booking_id) {
            $bookings = $bookings->where(DriverBooking::table() . ".id", $request->booking_id);
            $bookings = $bookings->first();
            return $this->api->json(true, "BOOKING", "Booking", [ "booking" => $bookings ]);
        } else {
            $bookings = $bookings->get();
            return $this->api->json(true, "BOOKINGS", "Bookings", [ "bookings" => $bookings ]);
        }

        
    }




    /** when driver starts to go to user's location */
    public function driverStart(Request $request)
    {
        $booking = DriverBooking::where("status", "driver_assigned")
            ->where("driver_id", $request->auth_driver->id)
            ->where("id", $request->booking_id)
            ->first();


        if(!$booking || $booking->datetime < Carbon::now() ) {
            return $this->api->json(true, "ERROR", "You are not allowed to start this booking.");
        }

        $booking->status = "driver_started";
        $booking->driver_started = date("Y-m-d H:i:s");
        $booking->save();

        $date = $booking->onlyDate(); $time = $booking->onlyTime();
        $booking->user->sendPushNotification("Driver is on they way", "Your Driver is on the way to your place. Share this OTP to start trip : {$booking->start_otp}", [], "com.capefox.cabrider.ui.activities.hireDriver.DriverPackagesActivity");


        return $this->api->json(true, "STARTED", "You have set your journey to user's place.");
    }



    /** start ride, use otp code */
    public function startRide(Request $request)
    {
        $booking = DriverBooking::where("status", "driver_started")
            ->where("driver_id", $request->auth_driver->id)
            ->where("id", $request->booking_id)
            ->first();


        if(!$booking || ($booking && $booking->start_otp != $request->start_otp) ) {
            return $this->api->json(false, "ERROR", "Entered Otp is not valid.");
        }
        

        $booking->status = "trip_started";
        $booking->trip_started = date("Y-m-d H:i:s");
        $booking->save();

        $date = $booking->onlyDate(); $time = $booking->onlyTime();
        $booking->user->sendPushNotification("Trip started", "Your Temp Driver trip started. Have a nice journey !", [], "com.capefox.cabrider.ui.activities.hireDriver.DriverPackagesActivity");


        return $this->api->json(true, "STARTED", "Trip started successfully.");
    }




    /**  end ride */
    public function endRide(Request $request)
    {
        /** fetch booking from db, if not found return error */
        $booking = DriverBooking::where("status", "trip_started")->where("driver_id", $request->auth_driver->id)->where("id", $request->booking_id)->first();
        if(!$booking) {
            return $this->api->json(true, "FAILED", "End trip failed");
        }


        /** update booking record */
        $booking->status = "trip_ended";
        $booking->trip_ended = date("Y-m-d H:i:s");

        /** calculate fare */
        $tax_percentage = Setting::get('vehicle_ride_fare_tax_percentage') ?: 0;
        list($fare, $night_charge) = $booking->package->calculateFare($booking->trip_started, $booking->trip_ended, $booking->user->timezone);
        $semi_total = $fare + $night_charge;

        /** coupon discount amount calculation */
        $coupon_discount = 0;
        $coupon = Coupon::where("code", $booking->coupon_code)->first();
        if($coupon) {
            $couponData = $coupon->calculateDiscount($semi_total);
            $semi_total = $couponData["total"];
            $coupon_discount = $couponData["coupon_discount"];
        }
        /** coupon discount amount calculation end*/

        /** calculating tax */
        $tax = $semi_total * ( $tax_percentage / 100 );
        $tax = Utill::formatAmountDecimalTwoWithoutRound($tax);

        /** calculaating semi total and total */
        $total = $semi_total + $tax;
        $total = Utill::formatAmountDecimalTwoWithoutRound($total);

        /** creting invoice */
        $invoice = new Invoice;
        $invoice->invoice_reference = $invoice->generateInvoiceReference();
        $invoice->payment_mode = $booking->payment_mode;
        $invoice->ride_fare = $fare;
        $invoice->night_charge = $night_charge;
        $invoice->access_fee = 0.00;
        $invoice->tax = $tax;
        $invoice->total = $total;
        $invoice->coupon_discount = $coupon_discount;
        $invoice->cancellation_charge = 0.00;
        $invoice->referral_bonus_discount = 0.00;
        $invoice->currency_type = Setting::get('currency_code');

        /** if cash payment mode then payment_status paid */
        if($booking->payment_mode == "CASH" || $total == 0) {
            $booking->payment_status = "PAID";
            $invoice->payment_status = "PAID";

            /** create transaction because payment successfull here */
            $transaction = new Transaction;
            $transaction->trans_id = $invoice->invoice_reference;
            $transaction->amount = $total;
            $transaction->currency_type = $invoice->currency_type;
            $transaction->gateway = "CASH";
            $transaction->payment_method = "COD";
            $transaction->status = Transaction::SUCCESS;
        }


        /** store everything to db now */
        try {

            DB::beginTransaction();

            /** if paymnet mode cash, then save transaction */
            if( isset($transaction) ) {
                $transaction->save();
                $invoice->transaction_table_id = $transaction->id;
            }

            /** save invoice */
            $invoice->save();

            /** adding invoice id to booking */
            $booking->invoice_id = $invoice->id;
            $booking->save();

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            return $this->api->json(false,'SEVER_ERROR', 'Internal server error try again.');
        }


        /** send invoice mail to user */
        $this->email->sendDriverBookingInvoiceEmail($booking);

        /** send push notification to user */
        $user = User::find($booking->user_id);
        $currencySymbol = Setting::get('currency_symbol');
        $user->sendPushNotification("Your trip ended", "We hope you enjoyed our service. Please make payment of {$currencySymbol}".$invoice->total, [], "com.capefox.cabrider.ui.activities.hireDriver.DriverPackagesActivity");
        $user->sendSms("We hope you enjoyed our ride service. Total payable amount is {$currencySymbol}".$invoice->total);

        /** send socket push to user */
        $this->socketIOClient->sendEvent([
            'to_ids' => $booking->user_id,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'booking_status_changed',
            'data' => [
                'booking_id' => $booking->id,
                'status' => $booking->status,
                'invoice' => $invoice->toArray(),
            ]
        ]);


        ProcessDriverInvoice::dispatch('driverbook', $booking->id);

        return $this->api->json(true, 'BOOKING_ENDED', 'Booking ended successfully', [
            'booking' => $booking,
            'invoice' => $invoice,
        ]);


    }




    public function rateUser(Request $request)
    {
        /** fetch booking from db, if not found return error */
        $booking = DriverBooking::where("status", "trip_ended")
            ->where("driver_id", $request->auth_driver->id)
            ->where("user_rating", 0)
            ->where("id", $request->booking_id)
            ->first();

        if(!$booking) {
            return $this->api->json(true, "FAILED", "Invalid booking id to rate user.");
        }


        /** validate rating number */
        if(!$request->has('rating') || !in_array($request->rating, [1, 2, 3, 4, 5])) {
            return $this->api->json(false, 'INVALID_RATING', 'You must have give rating within '.implode(',', [1, 2, 3, 4, 5]));
        }

        /** driver cannot give rating until user payment complete */
        if($booking->payment_status == "NOT_PAID") {
            return $this->api->json(false, 'USER_NOT_PAID', 'Ask user to pay before give rating');
        }


        /** updatig both driver and ride request table */
        try {

            \DB::beginTransaction();

            /** saving ride request rating */
            $booking->user_rating = $request->rating;
            $booking->save();  

            /** push user rating calculation to job */
            ProcessUserRating::dispatch($booking->user_id);

            \DB::commit();

        } catch(\Exception $e) {
            \DB::rollback();
            \Log::info('USER_RATING');
            \Log::info($e->getMessage());
            return $this->api->unknownErrResponse();
        }
        

        /** send user that you made the payment message */
        $user = $booking->user;
        $currencySymbol = Setting::get('currency_symbol');
        $websiteName = Setting::get('website_name');
        $invoice = $booking->invoice;
        if($booking->payment_status == "PAID") {
            $user->sendSms("Thank you!! We hope you enjoyed {$websiteName} service. See you next time.");
        } 

        return $this->api->json(true, 'RATED', 'User rated successfully.');

    }



}
