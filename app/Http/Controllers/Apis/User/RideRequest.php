<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use Illuminate\Http\Request;
use App\Models\RideRequest as Ride;
use Validator;


class RideRequest extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, Ride $rideRequest)
    {
        $this->api = $api;
        $this->rideRequest = $rideRequest;
    }




    /**
     * returns payment modes for ride requests
     */
    public function getPaymentModes(Request $request)
    {
        return $this->api->json(true, 'PAYMENT_MODES', 'Payment modes', [
            'payment_modes' => $this->rideRequest->getPaymentModes()
        ]);
    }



    /**
     * updates ride request payment mode
     * payment modes can be updated before driver accepted the request
     */
    public function updatePaymentMode(Request $request)
    {
        //if payment mode does not match 
        if(!in_array($request->payment_mode, $this->rideRequest->getPaymentModes())) {
            return $this->api->json(false, 'INVALID_PAYMENT_MODE', 'Invalid payment mode selected');
        }

        /**
         *  if request_id in invalid or request not belongs to user
         *  or request status is not allowed to change the payment status
         */
        $rideRequest = $this->rideRequest->where('id', $request->ride_request_id)
        ->whereIn('ride_status', $this->rideRequest->updatePaymentModeAllowedStatusList())
        ->first();

        if(!$rideRequest) {
            return $this->api->json(false, 'INVALID_RIDE_REQUEST', 'Invalid ride request'); 
        }

        $rideRequest->payment_mode = $request->payment_mode;
        $rideRequest->save();

        return $this->api->json(true, 'PAYMENT_MODE_UPDATED', 'Payment mode updated successfully'); 


    }







    /**
     * check previous on going ride request
     * if payment not done, not completed, rating not given etc etc
     */
    public function checkRideRequest(Request $request)
    {
        
        $rideRequest = $this->rideRequest
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
        ];

        //removing driver object relationship from ride request
        unset($rideRequest->driver);

        return $this->api->json(true, 'ONGOING_REQUEST', 'Ongoing request found', [
            'ride_request' => $rideRequest,
            'driver' => $driver
        ]);


    }





    /**
     * cancel ride request
     */
    public function cancelRideRequest(Request $request)
    {
        
        /**
         *  if request_id in invalid or request not belongs to user
         *  or request status is not allowed to canceled
         */
        $rideRequest = $this->rideRequest->where('id', $request->ride_request_id)
        ->whereIn('ride_status', $this->rideRequest->rideRequestCancelAllowedStatusList())
        ->first();

        if(!$rideRequest) {
            return $this->api->json(false, 'INVALID_RIDE_REQUEST', 'Invalid ride request'); 
        }

        $rideRequest->ride_status = Ride::USER_CANCELED;
        $rideRequest->save();

        return $this->api->json(true, 'RIDE_REQUEST_CANCELED', 'Ride Request canceled successfully'); 
           
    }


    

}
