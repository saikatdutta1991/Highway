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
        /** fetching vehicle service details by vehicle_type_id */
        $rFare = $this->rideFare->where('vehicle_type_id', $request->vehicle_type_id)->first();


        /** validate input params and vehile service */
        if(!is_numeric($request->distance) || 
            (!is_numeric($request->duration) || $request->duration < 0) 
            || !$rFare)
        {
            return $this->api->json(false, 'INVALID_INPUT_PARAMS', 'Invalid input params');
        }


        /** calculate fare by distance and duration */
        $distance = $request->distance / 1000; //meter to km
        $duration = intval($request->duration); //taking invalue from duration
        $fareData = $rFare->calculateFare($distance, 0); //duration no need to pass because in price calculator waiting time calculated


        $userId = $request->auth_user->id;

        /** calculating referral bonus discount, this block in if condition
         * because referral system can be disabled
        */
        if( ($bonusDeduction = $this->referral->deductBounus($userId, $fareData['total'])) !== false ) {
            $fareData['total'] = $bonusDeduction['total'];
            $fareData['bonusDiscount'] = $bonusDeduction['bonusDiscount'];
        }


        /** calculating cancellation charge */
        $cancellaionCharge = $this->cCharge->calculateCancellationCharge($userId);
        $fareData['total'] += $cancellaionCharge;
        $fareData['cancellation_charge'] = $cancellaionCharge;



        /** calculation for coupon on if coupon passed*/
        $fareData['coupon_discount'] = '0.00';
        if($request->coupon_code != '') {

            $validCoupon = $this->coupon->isValid($request->coupon_code, $userId, $coupon);
            if($validCoupon !== true) {
                return $this->api->json(false, $validCoupon['errcode'], $validCoupon['errmessage']);
            }

            //code comes here means coupon valid
            $couponDeductionRes = $coupon->calculateDiscount($fareData['total']);
            $fareData['total'] = $couponDeductionRes['total'];
            $fareData['coupon_discount'] = $couponDeductionRes['coupon_discount'];
        }
        
        /** calculation for coupon end*/


        $fareData['total'] = number_format(round($fareData['total']), 2, '.', '');
        return $this->api->json(true, 'FARE_DATA', 'Fare data fetched successfully', $fareData);

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
        $totalFare = $trip->adminRoute->base_fare + $trip->adminRoute->access_fee + $trip->adminRoute->tax_fee;
        $totalFare = $totalFare * $request->no_of_seats;

        $couponDeductionRes = $coupon->calculateDiscount($totalFare);
      

        return $this->api->json(true, 'VALID_COUPON', 'Coupon valid', [
            'total' => $this->utill->formatAmountDecimalTwo($totalFare),
            'no_of_seats' => $request->no_of_seats,
            'after_deduction' => $this->utill->formatAmountDecimalTwo($couponDeductionRes['total']),
            'coupon_discount' => $couponDeductionRes['coupon_discount']
        ]);

    }









}
