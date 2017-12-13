<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\Models\RideRequest as Ride;
use App\Repositories\SocketIOClient;
use App\Models\User;
use Validator;

class RideRequest extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, Ride $rideRequest, SocketIOClient $socketIOClient, User $user)
    {
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
        
       /*  $rideRequest = $this->rideRequest
        ->where('user_id', $request->auth_user->id)
        ->whereNotIn('ride_status', $this->rideRequest->notOngoigRideRequestStatusList())
        ->first();

        if(!$rideRequest) {
            return $this->api->json(false, 'NO_ONGOING_REQUEST_FOUND', 'No ongoing request');
        }

        $driver = [
            'id' => $rideRequest->driver->id,
            'fname' => $rideRequest->driver->fname,
            'lname' => $rideRequest->driver->lname,
            'country_code' => $rideRequest->driver->country_code,
            'mobile_number' => $rideRequest->driver->mobile_number,
            'latidue' => $rideRequest->driver->latitude,
            'longitude' => $rideRequest->driver->longitude,
        ]; */

        //removing driver object relationship from ride request
       /*  unset($rideRequest->driver);

        return $this->api->json(true, 'ONGOING_REQUEST', 'Ongoing request found', [
            'ride_request' => $rideRequest,
            'driver' => $driver
        ]); */


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




}
