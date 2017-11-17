<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use Illuminate\Http\Request;
use App\Models\RideFare;
use Validator;
use App\Models\User;

class PriceCalculator extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, User $user, RideFare $rideFare)
    {
        $this->api = $api;
        $this->user = $user;
        $this->rideFare = $rideFare;
    }


    /**
     * estimate price based on distance(km) and 
     */
    public function estimatePrice(Request $request)
    {
       
        if($request->distance == '' || !is_numeric($request->distance)) {
            return $this->api->json(false, 'INVALID_DISTANCE', 'Invalid distance');
        }

        if($request->duration == '' || !is_numeric($request->duration) || $request->duration < 0) {
            return $this->api->json(false, 'INVALID_DURATION', 'Invalid time duration');
        }


        $rFare = $this->rideFare->where('vehicle_type_id', $request->vehicle_type_id)->first();
        
        if(!$rFare) {
            return $this->api->json(false, 'FARE_NOT_SET', 'Fare not set by admin. Try again');
        }

        $fareData = $this->rideFare->calculateFare(
            $rFare, $request->distance, intval($request->duration)
        );

        return $this->api->json(true, 'FARE_DATA', 'Fare data fetched successfully', $fareData);
    }






}
