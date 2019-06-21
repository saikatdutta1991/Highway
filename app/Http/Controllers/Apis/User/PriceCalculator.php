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
use App\Models\RideCancellationCharge as CancellationCharge;
use App\Models\Coupons\Coupon;
use App\Models\Setting;
use App\Models\Trip\Trip as TripModel;

class PriceCalculator extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(CancellationCharge $cCharge, Api $api, User $user, RideFare $rideFare, Referral $referral)
    {
        $this->cCharge = $cCharge;
        $this->api = $api;
        $this->user = $user;
        $this->rideFare = $rideFare;
        $this->referral = $referral;
        $this->coupon = app('App\Models\Coupons\Coupon');
        $this->trip = app('App\Models\Trip\Trip');
        $this->utill = app('App\Repositories\Utill');
    }


    /**
     * estimate price based on distance(km) and 
     */
    public function estimatePrice(Request $request)
    {
        /** log the request */
        $this->api->log('estimate price reqeust', $request->all());

        /** fetching vehicle service fare details by service id */
        $serviceFare = RideFare::getServiceFareById($request->vehicle_type_id);


        /** validate input params and vehile service */
        if(!is_numeric($request->distance) ||  !$serviceFare)
        {
            return $this->api->json(false, 'INVALID_INPUT_PARAMS', 'Invalid input params');
        }


        /** calculate fare by distance, 
         * waittime not required in price calculator because it will be needed in after complete ride
         */
        $distance = $request->distance / 1000; // converting meter to km
        $fareDetails = $serviceFare->calculateCityRideFare(
            $request->auth_user->id, //user id
            $distance, //distance im km
            0, //wait time 
            $request->coupon_code //coupon code
        );


        /** log the fare */
        $this->api->log('estimate price', $fareDetails);


        if(isset($fareDetails['errcode'])) {
            return $this->api->json(false, $fareDetails['errcode'], $fareDetails['errmessage']);
        }


        return $this->api->json(true, 'FARE_DATA', 'Fare data fetched successfully', $fareDetails);

    }






    /**
     * validate coupon code for trip
     */
    public function validateCouponCodeForTrip(Request $request)
    {

        if($request->coupon_code == '' || $request->no_of_seats < 1) {
            return $this->api->json(false, 'INVALID_INPUT_PARAMS', 'Invalid input params');
        }

        /** fetch trip from db */
        $trip = $this->trip->where('id', $request->trip_id)->first();


        /** check if coupon code is valid */
        $validCoupon = $this->coupon->isValid($request->coupon_code, $request->auth_user->id, $coupon, 2);
        if($validCoupon !== true) {
            return $this->api->json(false, $validCoupon['errcode'], $validCoupon['errmessage']);
        }



        /** calculate total fare */
        $semi_total = $trip->adminRoute->base_fare + $trip->adminRoute->access_fee;
        $semi_total *= $request->no_of_seats;

        $couponDeductionRes = $coupon->calculateDiscount($semi_total);
        $semi_total = $couponDeductionRes['total'];
        

        $tax_percentage = Setting::get('vehicle_ride_fare_tax_percentage') ?: 0;
        $tax = ($semi_total * $tax_percentage) / 100; 
        $totalFare = $semi_total + $tax;
        $totalFare = $this->utill->formatAmountDecimalTwo($totalFare);


        return $this->api->json(true, 'VALID_COUPON', 'Coupon valid', [
            'total' => $totalFare,
            'no_of_seats' => $request->no_of_seats,
            'after_deduction' => $totalFare,
            'coupon_discount' => $couponDeductionRes['coupon_discount']
        ]);

    }









}
