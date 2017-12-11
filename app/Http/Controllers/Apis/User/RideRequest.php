<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use App\Models\Driver;
use Hash;
use Illuminate\Http\Request;
use App\Models\RideRequest as Ride;
use App\Models\Setting;
use App\Repositories\Utill;
use Validator;
use App\Models\VehicleType;

class RideRequest extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Setting $setting, Api $api, Ride $rideRequest, VehicleType $vehicleType, Driver $driver)
    {
        $this->setting = $setting;
        $this->api = $api;
        $this->rideRequest = $rideRequest;
        $this->vehicleType = $vehicleType;
        $this->driver = $driver;
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




    /**
     * get nearby drivers 
     * used for ride reqeust basically ?vehicle_type is optional
     */
    public function getNearbyDrivers(Request $request)
    {

        // taking latitude and longitude from request
        try {
            list($latitude, $longitude) = explode(',', $request->lat_long);
        } catch(\Exception $e) {
            return $this->api->json(false, 'LAT_LONG_FORMAT_INVALID', 'Latitude,longitude format invalid');
        }


        // validating vehicle type if exists
        if($request->vehicle_type != '' && !in_array($request->vehicle_type, $this->vehicleType->allCodes())) {
            return $this->api->json(false, 'VEHIVLE_TYPE_INVALID', 'Vehicle type is invalid.');
        }
        

        $radious = $this->setting->get('ride_request_driver_search_radious')?:0;
        $drivers = $this->driver->getNearbyDriversBuilder($latitude, $longitude, $radious);
        
        //if vehicle type is passed then filter drivers
        if($request->vehicle_type) {
            $drivers = $drivers->where($this->driver->getTableName().'.vehicle_type', $request->vehicle_type);
        }

        //filter drivers approved, available, connected to socket
        $dt = $this->driver->getTableName();
        $nearbyDriversDetails = $drivers->where($dt.'.is_approved', 1)
        ->where($dt.'.is_available', 1)
        ->where($dt.'.is_connected_to_socket', 1)
        ->orderBy($dt.'.rating', 'desc')
        ->select([
            $dt.'.id',            
            $dt.'.latitude',            
            $dt.'.longitude',
            $dt.'.vehicle_type',            
        ])
        ->take(50)->get();

        return $this->api->json(true, 'NEARBY_DRIVERS', 'Nearby drivers', [
            'drivers' => $nearbyDriversDetails
        ]);

    }


    

}
