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
use App\Repositories\Referral;

class PriceCalculator extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, User $user, RideFare $rideFare, Referral $referral)
    {
        $this->api = $api;
        $this->user = $user;
        $this->rideFare = $rideFare;
        $this->referral = $referral;
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

        //convert distance meter into km
        $distance = $request->distance / 1000;

        $fareData = $rFare->calculateFare(
            $distance, intval($request->duration)
        );

        $res = $this->referral->deductBounus($request->auth_user->id, $fareData['total']);
       
        if($res !== false) {
            $fareData['total'] = $res['total'];
            $fareData['bonusDiscount'] = $res['bonusDiscount'];
        }
        



        return $this->api->json(true, 'FARE_DATA', 'Fare data fetched successfully', $fareData);
    }






}
