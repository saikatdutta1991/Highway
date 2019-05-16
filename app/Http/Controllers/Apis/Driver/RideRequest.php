<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use App\Repositories\Email;
use App\Jobs\ProcessUserRating;
use Illuminate\Http\Request;
use App\Models\RideFare;
use App\Models\RideRequest as Ride;
use App\Models\RideRequestInvoice as RideInvoice;
use App\Models\Setting;
use App\Repositories\SocketIOClient;
use App\Models\Transaction;
use App\Models\User;
use App\Models\VehicleType;
use Validator;
use App\Repositories\Referral;
use App\Models\RideCancellationCharge as CancellationCharge;
use App\Models\Coupons\Coupon;
use App\Models\Coupons\UserCoupon;

class RideRequest extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(CancellationCharge $cCharge, Setting $setting, Transaction $transaction, Email $email, VehicleType $vehicleType, RideFare $rideFare, RideInvoice $rideInvoice, Api $api, Ride $rideRequest, SocketIOClient $socketIOClient, User $user, Referral $referral)
    {
        $this->cCharge = $cCharge;
        $this->setting = $setting;
        $this->transaction = $transaction;
        $this->email = $email;
        $this->vehicleType = $vehicleType;
        $this->rideFare = $rideFare;
        $this->rideInvoice = $rideInvoice;
        $this->api = $api;
        $this->rideRequest = $rideRequest;
        $this->socketIOClient = $socketIOClient;
        $this->user = $user;
        $this->referral = $referral;
        $this->coupon = app('App\Models\Coupons\Coupon');
        $this->userCoupon = app('App\Models\Coupons\UserCoupon');
    }



    
    /**
     * accept or reject user ride request
     * make driver avaiable status 0
     * send push notification and socket notification to user
     */
    public function acceptRideRequest(Request $request)
    {

        /**
         * check if user_id, ride_request_id and ride request status has been initiated state
         */
        $rideRequest = $this->rideRequest->where('user_id', $request->user_id)
        ->where('id', $request->ride_request_id)
        ->where('ride_status', Ride::INITIATED)
        ->first();

        if(!$rideRequest) {
            return $this->api->json(false, 'INVALID_RIDE_REQUEST', 'Invalid ride request'); 
        }


        //check if request has been already accepted by another driver
        if($rideRequest->driver_id != 0) {
            return $this->api->json(false, 'RIDE_REQUEST_ALREADY_ACCEPTED', 'Request might have accepted by another driver.'); 
        }
        

        $authDriver = $request->auth_driver;

        try {

            DB::beginTransaction();
            //setting driver id in ride request table
            $rideRequest->driver_id = $authDriver->id;
            //chaning ride request status to driver accepted
            $rideRequest->ride_status = Ride::DRIVER_ACCEPTED;
            $rideRequest->save();

            //chaning driver availability to 0
            $authDriver->is_available = 0;
            $authDriver->save();

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            return $this->api->json(false,'SEVER_ERROR', 'Internal server error try again.');
        }


        // same notification data to be sent to user
        $notificationData = [
            'ride_request_id' => $rideRequest->id,
            'ride_status' => $rideRequest->ride_status,
            'driver' => [
                'id' => $authDriver->id,
                'fname' => $authDriver->fname,
                'lname' => $authDriver->lname,
                'vehicle_type' => $authDriver->vehicle_type,
                'country_code' => $authDriver->country_code,
                'mobile_number' => $authDriver->mobile_number,
                'vehicle_number' => $authDriver->vehicle_number,
                'profile_photo_url' => $authDriver->profilePhotoUrl(),
                'latitude' => $authDriver->latitude,
                'longitude' => $authDriver->longitude,
                'rating' => $authDriver->rating,
            ]
        ];



        /**
         * send push notification to user
         */
        $user = $this->user->find($rideRequest->user_id);
        $user->sendPushNotification("Your ride accepted", "Driver {$authDriver->fname} has accepted your ride request");


        /**
         * send socket push to user
         */
        $this->socketIOClient->sendEvent([
            'to_ids' => $rideRequest->user_id,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'ride_request_status_changed',
            'data' => $notificationData
        ]);
        
        
        return $this->api->json(true, 'RIDE_REQUEST_ACCEPTED', 'Ride request accepted successfully');

    }





    /**
     * check previous on going ride request
     * if payment not done, not completed, rating not given etc etc
     */
    public function checkRideRequest(Request $request)
    {
        
        //check any ongoing request is there or not
        $rideRequest = $this->rideRequest
        ->where('driver_id', $request->auth_driver->id)
        ->whereNotIn('ride_status', $this->rideRequest->notOngoigRideRequestStatusListDriver())
        ->first();

        if(!$rideRequest) {
            //check for ride complated or trip ended but no user rated
            $rideRequest = $this->rideRequest
            ->where('driver_id', $request->auth_driver->id)
            ->whereIn('ride_status', [Ride::TRIP_ENDED, Ride::COMPLETED])
            ->where('user_rating', 0)
            ->first();
        }

        if(!$rideRequest) {
            return $this->api->json(false, 'NO_ONGOING_REQUEST_FOUND', 'No ongoing request');
        }


        $user = [
            'id' => $rideRequest->user->id,
            'fname' => $rideRequest->user->fname,
            'lname' => $rideRequest->user->lname,
            'country_code' => $rideRequest->user->country_code,
            'mobile_number' => $rideRequest->user->mobile_number,
            'rating' => $rideRequest->user->rating,
        ];

        //take invoice if invoice is ready
        $invoice = []; //init invoice array empty
        if($rideRequest->ride_invoice_id != 0) {
            $invoice = $rideRequest->invoice->toArray();
            unset($rideRequest->invoice);
        }
        

        //removing user object relationship from ride request
        unset($rideRequest->user);

        return $this->api->json(true, 'ONGOING_REQUEST', 'Ongoing request found', [
            'ride_request' => $rideRequest,
            'user' => $user,
            'invoice' => $invoice,
        ]);


    }





    /**
     * change ride request status
     * changes after driver accepts intermediate status until start
     * example DRIVER_STARTED, DRIVER_REACHED
     */
    public function changeRideRequestStatus(Request $request)
    {
        /**
         *  if request_id in invalid or request not belongs to driver
         *  or request status is not allowed to canceled
         */
        $rideRequest = $this->rideRequest->where('id', $request->ride_request_id)
        ->where('driver_id', $request->auth_driver->id)
        ->whereIn('ride_status', [Ride::DRIVER_ACCEPTED, Ride::DRIVER_STARTED])
        ->first();

        if(!$rideRequest) {
            return $this->api->json(false, 'INVALID_RIDE_REQUEST', 'Invalid ride request'); 
        }


        /**
         * validation status paramter input
         */
        if(!in_array($request->status, [Ride::DRIVER_STARTED, Ride::DRIVER_REACHED])) {
            return $this->api->json(false, 'INVALID_RIDE_STATUS', 'Invalid ride status'); 
        }


        $authDriver = $request->auth_driver;

        //changing ride request status
        $rideRequest->ride_status = $request->status;

        /** add driver_started_time */
        if($request->status == Ride::DRIVER_STARTED) {
            $rideRequest->driver_started_time = date('Y-m-d H:i:s');
        }

        /** add driver reached to pickup location time */
        if($request->status == Ride::DRIVER_REACHED) {
            $rideRequest->driver_reached_time = date('Y-m-d H:i:s');
        }

        $rideRequest->save();

      
        // same notification data to be sent to user
        $notificationData = [
            'ride_request_id' => $rideRequest->id,
            'ride_status' => $rideRequest->ride_status,
        ];


        /**
         * send push notification to user
         */
        $pushNotificationTitle = ($request->status == Ride::DRIVER_STARTED) ? "Driver is on the way" : "Driver reached";
        $pushNotificationText = ($request->status == Ride::DRIVER_STARTED) ? "Driver {$authDriver->fname} is on the way" : "Driver {$authDriver->fname} has reached the location";
        $user = $this->user->find($rideRequest->user_id);
        $user->sendPushNotification($pushNotificationTitle, $pushNotificationText);


        /**
         * send socket push to user
         */
        $this->socketIOClient->sendEvent([
            'to_ids' => $rideRequest->user_id,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'ride_request_status_changed',
            'data' => $notificationData
        ]);

        return $this->api->json(true, 'RIDE_REQUEST_STATUS_CHANGED', 'Ride Request status changed'); 

    }








    /**
     * cancel ride request
     */
    public function cancelRideRequest(Request $request)
    {
       
        /**
         *  if request_id in invalid or request not belongs to driver
         *  or request status is not allowed to canceled
         */
        $rideRequest = $this->rideRequest->where('id', $request->ride_request_id)
        ->where('driver_id', $request->auth_driver->id)
        ->whereIn('ride_status', $this->rideRequest->driverRideRequestCancelAllowedStatusList())
        ->first();

        if(!$rideRequest) {
            return $this->api->json(false, 'INVALID_RIDE_REQUEST', 'Invalid ride request'); 
        }


        /** check driver cancel limit */
        $todaysCancelCount = $this->rideRequest->where('driver_id', $request->auth_driver->id)
            ->where('ride_status', Ride::DRIVER_CANCELED)
            ->where('created_at', 'like', date('Y-m-d').'%')
            ->count();

        $cancelLimitCount = $this->setting->get('driver_cancel_ride_request_limit') ?: 0;
        $this->api->log('cancel limit count', $cancelLimitCount);
        $this->api->log('todays cancel count', $todaysCancelCount);
        if($todaysCancelCount >= $cancelLimitCount) {
            return $this->api->json(false, 'CANCEL_NOW_ALLOWED', 'You have exceeded the cancellation limit for today'); 
        }





        $authDriver = $request->auth_driver;

        try {

            DB::beginTransaction();

            $rideRequest->ride_status = Ride::DRIVER_CANCELED;
            $rideRequest->save();

            //chaning driver availability to 0
            $authDriver->is_available = 1;
            $authDriver->save();

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            return $this->api->json(false,'SEVER_ERROR', 'Internal server error try again.');
        }


        
        // same notification data to be sent to user
        $notificationData = [
            'ride_request_id' => $rideRequest->id,
            'ride_status' => $rideRequest->ride_status,
        ];


        /**
         * send push notification to user
         */
        $user = $this->user->find($rideRequest->user_id);
        $user->sendPushNotification("Driver canceled ride", "Driver {$authDriver->fname} has canceled your ride request");


        /**
         * send socket push to user
         */
        $this->socketIOClient->sendEvent([
            'to_ids' => $rideRequest->user_id,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'ride_request_status_changed',
            'data' => $notificationData,
            'store_messsage' => true
        ]);


        return $this->api->json(true, 'RIDE_REQUEST_CANCELED', 'Ride Request canceled successfully'); 
           
    }







    /**
     * start trip must becalled after driver reached user location
     * save start trip time
     */
    public function startRideRequest(Request $request)
    {
        /**
         *  if request_id in invalid or request not belongs to driver
         */
        $rideRequest = $this->rideRequest->where('id', $request->ride_request_id)
        ->where('driver_id', $request->auth_driver->id)
        ->whereIn('ride_status', [Ride::DRIVER_REACHED])
        ->first();

        if(!$rideRequest) {
            return $this->api->json(false, 'INVALID_RIDE_REQUEST', 'Invalid ride request'); 
        }


        //change ride request status to TRIP_STARTED
        $rideRequest->ride_status = Ride::TRIP_STARTED;
        $rideRequest->ride_start_time = date('Y-m-d H:i:s');
        $rideRequest->save();


        // same notification data to be sent to user
        $notificationData = [
            'ride_request_id' => $rideRequest->id,
            'ride_status' => $rideRequest->ride_status,
        ];


        /**
         * send push notification to user
         */
        $user = $this->user->find($rideRequest->user_id);
        $user->sendPushNotification("Ride started", "Your trip has been started. Enjoy your ride.");


        /**
         * send socket push to user
         */
        $this->socketIOClient->sendEvent([
            'to_ids' => $rideRequest->user_id,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'ride_request_status_changed',
            'data' => $notificationData
        ]);


        return $this->api->json(true, 'RIDE_REQUEST_STRATED', 'Ride Request has been started'); 


    }





    /**
     * end trip after trip started
     * parameters take ditance, time, request id etc
     * generates invoice 
     * generate transaction if cash payment
     * if ride request status is completed only started
     */
    public function endRideRequest(Request $request)
    {

        /** fetching ride request from db by ride request table id */
        $rideRequest = $this->rideRequest->where('id', $request->ride_request_id)
        ->where('driver_id', $request->auth_driver->id)
        ->whereIn('ride_status', [Ride::TRIP_STARTED])
        ->first();

        if(!$rideRequest || !$request->has('ride_distance')) {
            return $this->api->json(false, 'INVALID_RIDE_REQUEST', 'Invalid ride request'); 
        }
        

        /** converting ride_request_distance meter to km and updating reqeust object*/
        $request->request->add(['ride_distance' => ($request->ride_distance/1000) ]);

        /** fetching ride fare by vehile type id */        
        $rideFare = $this->rideFare->where(
            'vehicle_type_id', $this->vehicleType->getIdByCode($rideRequest->ride_vehicle_type)
        )->first();

        $rideEndTime = date('Y-m-d H:i:s');
        $rideTime = app('UtillRepo')->getDiffMinute($rideRequest->ride_start_time, $rideEndTime);
        $rideWaitTiime = app('UtillRepo')->getDiffMinute($rideRequest->driver_reached_time, $rideRequest->ride_start_time);


        /** caltulating basic fare details */
        $fare = $rideFare->calculateFare($request->ride_distance, $rideWaitTiime);



        /** calculate cancellation charge */
        $cChargeAmt = $this->cCharge->calculateCancellationCharge($rideRequest->user_id);
        $fare['total'] += $cChargeAmt;
        $fare['cancellation_charge'] = $cChargeAmt;
        $this->cCharge->clearCharges($rideRequest->user_id);
        /** end calculate cancellation charge */



        /** calculate referral bonus */
        $bonusData = $this->referral->deductBounus($rideRequest->user_id, $fare['total']);
       
        if($bonusData !== false) {
            $fare['total'] = $bonusData['total'];
            $fare['bonusDiscount'] = $bonusData['bonusDiscount'];
        }
        /** end calculate referral bonus */
        


        /** calculate coupon discount if applied block */
        $fare['coupon_discount'] = 0.00;
        if($rideRequest->applied_coupon_id) {

            $coupon = $this->coupon->find($rideRequest->applied_coupon_id);
            $couponDeductionRes = $coupon->calculateDiscount($fare['total']);
            $fare['total'] = $couponDeductionRes['total'];
            $fare['coupon_discount'] = $couponDeductionRes['coupon_discount'];

        }
        /** calculate coupon discount if applied block end*/



        /** rounding total fare, dont change total after this*/
        $fare['total'] = round($fare['total']);

        
        try{
            DB::beginTransaction();

            /** insert coupon used by user in db */
            if($rideRequest->applied_coupon_id) {
                $userCoupon = new $this->userCoupon;
                $userCoupon->user_id = $rideRequest->user_id;
                $userCoupon->coupon_id = $rideRequest->applied_coupon_id;
                $userCoupon->save();
            }


            //updating ride request table
            $rideRequest->ride_distance = $request->ride_distance;
            $rideRequest->ride_time = $rideTime;
            $rideRequest->estimated_fare = $fare['total'];
            $rideRequest->ride_end_time = $rideEndTime;
            $rideRequest->ride_status = Ride::TRIP_ENDED;   

            
            //creting invoice
            $invoice = new $this->rideInvoice;
            $invoice->invoice_reference = $this->rideInvoice->generateInvoiceReference();
            $invoice->payment_mode = $rideRequest->payment_mode;
            $invoice->ride_fare = $fare['ride_fare'];
            $invoice->access_fee = $fare['access_fee'];
            $invoice->tax = $fare['taxes'];
            $invoice->total = $fare['total'];

            /** cancellation charge added to invoice */
            $invoice->cancellation_charge = $fare['cancellation_charge'];

            /** if referral bonus */ 
            if(isset($fare['bonusDiscount'])) {
                $invoice->referral_bonus_discount = $fare['bonusDiscount'];
            }

            /** coupon discount added to invoice */
            $invoice->coupon_discount = $fare['coupon_discount'];

            
            $invoice->currency_type = $this->setting->get('currency_code');

            list($invoiceImagePath, $invoiceImageName) = $invoice->saveInvoiceMapImage($rideRequest);
            $invoice->invoice_map_image_path = $invoiceImagePath;
            $invoice->invoice_map_image_name = $invoiceImageName;

            //if cash payment mode then payment_status paid
            if($rideRequest->payment_mode == Ride::CASH || $fare['total'] == 0) {
                $rideRequest->payment_status = Ride::PAID;
                $rideRequest->ride_status = Ride::COMPLETED;
                $invoice->payment_status = Ride::PAID;

                //create transaction because payment successfull here
                $transaction = new $this->transaction;
                $transaction->trans_id = $invoice->invoice_reference;
                $transaction->amount = $fare['total'];
                $transaction->currency_type = $this->setting->get('currency_code');
                $transaction->gateway = Ride::CASH;
                $transaction->payment_method = Ride::COD;
                $transaction->status = Transaction::SUCCESS;
                $transaction->save();

                //add transaciton_table_id in invoice
                $invoice->transaction_table_id = $transaction->id;

            }

            $invoice->save();

            //adding invoice id to ride request
            $rideRequest->ride_invoice_id = $invoice->id;
            $rideRequest->save();


            /** now deduct referral bonus amount from user */
            if(isset($fare['bonusDiscount'])) {
                $userReferralRecord = $this->referral->createReferralCodeIfNotExists('user', $rideRequest->user_id);
                $userReferralRecord->bonus_amount -= $fare['bonusDiscount'];
                $userReferralRecord->save();
            }


            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            \Log::info("END_RIDE_REQUEST_ERROR");
            \Log::info($e->getMessage().', file: '.$e->getFile().' line: '.$e->getLine());
            return $this->api->json(false,'SEVER_ERROR', 'Internal server error try again.');
        }


        //send invoice if paid
        if($rideRequest->payment_status == Ride::PAID) {
            $this->email->sendUserRideRequestInvoiceEmail($rideRequest);
        }
        

        // same notification data to be sent to user
        $notificationData = [
            'ride_request_id' => $rideRequest->id,
            'ride_status' => $rideRequest->ride_status,
            'invoice' => $invoice->toArray(),
        ];


        /**
         * send push notification to user
         */
        $user = $this->user->find($rideRequest->user_id);
        $currencySymbol = $this->setting->get('currency_symbol');
        $user->sendPushNotification("Your ride ended", "We hope you enjoyed our ride service. Please make payment of {$currencySymbol}".$invoice->total);
        $user->sendSms("We hope you enjoyed our ride service. Please make payment of {$currencySymbol}".$invoice->total);

        /**
         * send socket push to user
         */
        $this->socketIOClient->sendEvent([
            'to_ids' => $rideRequest->user_id,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'ride_request_status_changed',
            'data' => $notificationData
        ]);



        /**
         * dont call invoice save method after this
         */
        $invoice->map_url = $invoice->getStaticMapUrl();

        return $this->api->json(true, 'RIDE_REQUEST_ENDED', 'Ride request ended successfully', [
            'ride_request' => $rideRequest,
            'invoice' => $invoice,
        ]);

    }





    /**
     * give rating to user and complete the request
     */
    public function rateUser(Request $request)
    {

        //find ride request 
        $rideRequest = $this->rideRequest->where('id', $request->ride_request_id)
        ->where('driver_id', $request->auth_driver->id)
        ->whereIn('ride_status', [Ride::TRIP_ENDED, Ride::COMPLETED])
        ->first();


        if(!$rideRequest || !$rideRequest->user) {
            return $this->api->json(false, 'INVALID_REQUEST', 'Invalid Request, Try again.');
        }

        //validate rating number
        if(!$request->has('rating') || !in_array($request->rating, Ride::RATINGS)) {
            return $this->api->json(false, 'INVALID_RATING', 'You must have give rating within '.implode(',', Ride::RATINGS));
        }


        //updatig both driver and ride request table
        try {

            \DB::beginTransaction();

            //saving ride request rating
            $rideRequest->user_rating = $request->rating;
            $rideRequest->save();  

            /** push user rating calculation to job */
            ProcessUserRating::dispatch($rideRequest->user_id);


            //making availble driver
            $driver = $rideRequest->driver;
            $driver->is_available = 1;
            $driver->save();



            \DB::commit();

        } catch(\Exception $e) {
            \DB::rollback();
            \Log::info('USER_RATING');
            \Log::info($e->getMessage());
            return $this->api->unknownErrResponse();
        }
        

        return $this->api->json(true, 'RATED', 'User rated successfully.');

    }   





    /**
     * this returns ride request histories
     */
    public function getHistories(Request $request)
    {
        //takes trip ended, user cancelled, driver cancelled ride requests
        $rideRequests = $this->rideRequest->where('driver_id', $request->auth_driver->id)
        ->whereIn('ride_status', [Ride::COMPLETED, Ride::TRIP_ENDED, Ride::USER_CANCELED, Ride::DRIVER_CANCELED])
        ->with(['user', 'invoice'])
        ->orderBy('updated_at', 'desc')
        ->paginate(100);

        $rideRequests->map(function($rideRequest){
            
            if($rideRequest->invoice) {
                $rideRequest->invoice['map_url'] = $rideRequest->invoice->getStaticMapUrl();
            }
            
        });

        return $this->api->json(true, 'RIDE_REQUEST_HISTORIES', 'Ride request histories', [
            'ride_requests'=> $rideRequests->items(),
            'paging' => [
                'total' => $rideRequests->total(),
                'has_more' => $rideRequests->hasMorePages(),
                'next_page_url' => $rideRequests->nextPageUrl()?:'',
                'count' => $rideRequests->count(),
            ]
        ]);


    }



}
