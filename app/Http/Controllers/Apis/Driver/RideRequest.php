<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\Models\RideFare;
use App\Models\RideRequest as Ride;
use App\Models\RideRequestInvoice as RideInvoice;
use App\Repositories\SocketIOClient;
use App\Models\User;
use App\Models\VehicleType;
use Validator;

class RideRequest extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(VehicleType $vehicleType, RideFare $rideFare, RideInvoice $rideInvoice, Api $api, Ride $rideRequest, SocketIOClient $socketIOClient, User $user)
    {
        $this->vehicleType = $vehicleType;
        $this->rideFare = $rideFare;
        $this->rideInvoice = $rideInvoice;
        $this->api = $api;
        $this->rideRequest = $rideRequest;
        $this->socketIOClient = $socketIOClient;
        $this->user = $user;
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
            ]
        ];



        /**
         * send push notification to user
         */
        $user = $this->user->find($rideRequest->user_id);
        $user->sendPushNotification("Driver {$authDriver->fname} has accepted your ride request", $notificationData);


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
        
        $rideRequest = $this->rideRequest
        ->where('user_id', $request->auth_driver->id)
        ->whereNotIn('ride_status', $this->rideRequest->notOngoigRideRequestStatusList())
        ->first();

        if(!$rideRequest) {
            return $this->api->json(false, 'NO_ONGOING_REQUEST_FOUND', 'No ongoing request');
        }


        $user = [
            'id' => $rideRequest->user->id,
            'fname' => $rideRequest->user->fname,
            'lname' => $rideRequest->user->lname,
            'country_code' => $rideRequest->user->country_code,
            'mobile_number' => $rideRequest->user->mobile_number
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
        $rideRequest->save();

      
        // same notification data to be sent to user
        $notificationData = [
            'ride_request_id' => $rideRequest->id,
            'ride_status' => $rideRequest->ride_status,
        ];


        /**
         * send push notification to user
         */
        $pushNotificationText = ($request->status == Ride::DRIVER_STARTED) ? "Driver {$authDriver->fname} is on the way" : "Driver {$authDriver->fname} has reached the location";
        $user = $this->user->find($rideRequest->user_id);
        $user->sendPushNotification($pushNotificationText, $notificationData);


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
        $user->sendPushNotification("Driver {$authDriver->fname} has canceled your ride request", $notificationData);


        /**
         * send socket push to user
         */
        $this->socketIOClient->sendEvent([
            'to_ids' => $rideRequest->user_id,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'ride_request_status_changed',
            'data' => $notificationData
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
        $user->sendPushNotification("Trip has been started", $notificationData);


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
        //find ride request 
        $rideRequest = $this->rideRequest->where('id', $request->ride_request_id)
        ->where('driver_id', $request->auth_driver->id)
        ->whereIn('ride_status', [Ride::TRIP_STARTED])
        ->first();

        if(!$rideRequest) {
            return $this->api->json(false, 'INVALID_RIDE_REQUEST', 'Invalid ride request'); 
        }
        
        if(!$request->has('ride_distance') || !$request->has('ride_time')) {
            return $this->api->json(false, 'PARAMTERS_ARE_MISSING', 'Some input parameters are missing');
        }

        //chaning ride_request_distance meter to km
        $request->request->add(['ride_distance' => ($request->ride_distance/1000) ]);

        //getting rideFare object by vehicle type id
        $vTypeId = $this->vehicleType->getIdByCode($rideRequest->ride_vehicle_type);
        $rideFare = $this->rideFare->where('vehicle_type_id', $vTypeId)->first();
        $fare = $rideFare->calculateFare($request->ride_distance, $request->ride_time);

        
        try{
            DB::beginTransaction();

            //updating ride request table
            $rideRequest->ride_distance = $request->ride_distance;
            $rideRequest->ride_time = $request->ride_time;
            $rideRequest->estimated_fare = $fare['total'];
            $rideRequest->ride_end_time = date('Y-m-d H:i:s');
            $rideRequest->ride_status = Ride::TRIP_ENDED;   

            //creting invoice
            $invoice = new $this->rideInvoice;
            $invoice->invoice_reference = $this->rideInvoice->generateInvoiceReference();
            $invoice->payment_mode = $rideRequest->payment_mode;
            $invoice->ride_fare = $fare['ride_fare'];
            $invoice->access_fee = $fare['access_fee'];
            $invoice->tax = $fare['taxes'];
            $invoice->total = $fare['total'];

            //if cash payment mode then payment_status paid
            if($rideRequest->payment_mode == Ride::CASH) {
                $rideRequest->payment_status = Ride::PAID;
                $invoice->payment_status = Ride::PAID;

                //send invoice if paid

            }

            $invoice->save();

            //adding invoice id to ride request
            $rideRequest->ride_invoice_id = $invoice->id;
            $rideRequest->save();


            DB::commit();
        } catch(\Exception $e) {
            DB::rollback();
            \Log::info("END_RIDE_REQUEST_ERROR");
            \Log::info($e->getMessage().', file: '.$e->getFile().' line: '.$e->getLine());
            return $this->api->json(false,'SEVER_ERROR', 'Internal server error try again.');
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
        $user->sendPushNotification("Your trip has been ended. Please make payment of ".$invoice->total, $notificationData);


        /**
         * send socket push to user
         */
        $this->socketIOClient->sendEvent([
            'to_ids' => $rideRequest->user_id,
            'entity_type' => 'user', //socket will make it uppercase
            'event_type' => 'ride_request_status_changed',
            'data' => $notificationData
        ]);



        return $this->api->json(true, 'RIDE_REQUEST_ENDED', 'Ride request ended successfully', [
            'ride_request' => $rideRequest,
            'invoice' => $invoice,
        ]);

    }





}
