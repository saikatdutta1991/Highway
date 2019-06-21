<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Utill;
use Cache;
use Log;
use App\Models\Setting;
use App\Models\User;

class RideFare extends Model
{

    protected $table = 'ride_fares';

    public function getTableName()
    {
        return $this->table;
    }


    /** 
     * returns service fare cachec key
     */
    protected static function serviceFareCacheKey($serviceid)
    {
        return "service_fare_.{$serviceid}";
    }



    /**
     * returns service fare model object by id
     */
    public static function getServiceFareById($serviceid)
    {
        return Cache::rememberForever(RideFare::serviceFareCacheKey($serviceid), function() use($serviceid) {
            Log::info("RideFare::getFareByServiceId() -> Retriving service fare from db key : {$serviceid}");
            return RideFare::where('vehicle_type_id', $serviceid)->first();
        });
    }






    /**
     * calculate/estimate price
     * distance in km(s)
     * $avgDuration in minute(s)
     * call this method if fare object fetched
     */
    public function calculateCityRideFare($userid, $distance, $waittime, $couponCode)
    {
        /** adding base price always */
        $ride_fare = $this->base_price;

        /** calculating first specific distance price . eg: first 2 km 10 rs. */
        if($distance < $this->first_distance) {
            $first_distance = $distance;
            $after_first_distance = 0;
        } else {
            $first_distance = $this->first_distance;
            $after_first_distance = $distance - $this->first_distance;
        }

        $first_distance_price = Utill::formatAmountDecimalTwoWithoutRound($this->first_distance_price);
        $after_first_distance_price = Utill::formatAmountDecimalTwoWithoutRound($this->after_first_distance_price * $after_first_distance);
        $wait_time_price = Utill::formatAmountDecimalTwoWithoutRound($waittime * $this->wait_time_price);

        /** ride fare on first distance price, after first distance price, wait time price */
        $ride_fare += $first_distance_price + $after_first_distance_price + $wait_time_price;
        $ride_fare = Utill::formatAmountDecimalTwoWithoutRound($ride_fare);

        /** access fee */
        $access_fee = $this->access_fee;

        /** semi total calculated on ride fare + access fee */
        $semi_total = $ride_fare + $access_fee;



        /** fetch user form db, and calcualte bonus disocunt, coupon disount, and cancellation charge */
        $user = User::find($userid);
        
        $bonusDiscount = '0.00';
        $cancellation_charge = '0.00';
        $coupon_discount = '0.00';

        if($user) {

            /** calculating referral bonus discount, this block in if condition
             * because referral system can be disabled
            */
            $bonusDeduction = app('App\Repositories\Referral')->deductBounus($user->id, $semi_total);
            if( $bonusDeduction !== false ) {
                $semi_total = $bonusDeduction['total'];
                $bonusDiscount = $bonusDeduction['bonusDiscount'];
            }

            
            /** calculating cancellation charge */
            $cancellation_charge = app('App\Models\RideCancellationCharge')->calculateCancellationCharge($user->id);
            $semi_total += $cancellation_charge;


            /** calculation for coupon on if coupon passed*/
            if($couponCode != '') {

                $validCoupon = app('App\Models\Coupons\Coupon')->isValid($couponCode, $user->id, $coupon);

                if($validCoupon !== true) {
                    return ['errcode' => $validCoupon['errcode'], 'errmessage' => $validCoupon['errmessage']];
                }
    
                //code comes here means coupon valid
                $couponDeductionRes = $coupon->calculateDiscount($semi_total);
                $semi_total = $couponDeductionRes['total'];
                $coupon_discount = $couponDeductionRes['coupon_discount'];
            }
            
       
        }



        /** calculat tax on semi total */
        $tax_percentage = Setting::get('vehicle_ride_fare_tax_percentage') ?: 0;
        $tax = $semi_total * ( $tax_percentage / 100 );
        $tax = Utill::formatAmountDecimalTwoWithoutRound($tax);

        /** calculate total on semi total + tax */
        $total = $semi_total + $tax;
        $total = Utill::formatAmountDecimalTwoWithoutRound(round($total));


        return [
            'ride_fare' => $ride_fare,
            'ride_fare_details' => [
                'base_price' => $this->base_price,
                'first_distance' => $first_distance,
                'first_distance_price' => $first_distance_price,
                'after_first_distance' => $after_first_distance,
                'after_first_distance_price' => $after_first_distance_price,
                'wait_time_price' => $wait_time_price,
            ],
            'access_fee' => $access_fee,
            'bonusDiscount' => $bonusDiscount,
            'cancellation_charge' => $cancellation_charge,
            'coupon_discount' => $coupon_discount,
            'tax_percentage' => $tax_percentage,
            'taxes' => $tax,
            'total' => $total
        ];
     
    }



    /**
     * add/update ride fare
     */
    public function addOrUpdateRideFare($vehicleTypeId, $data = [])
    {
        $rFare = $this->where('vehicle_type_id', $vehicleTypeId)->first() ?: new $this;

        $rFare->vehicle_type_id = $vehicleTypeId;
        $rFare->minimun_price = isset($data['minimun_price']) ? $data['minimun_price'] : 0.00;
        $rFare->access_fee = isset($data['access_fee']) ? $data['access_fee'] : 0.00;
        $rFare->base_price = isset($data['base_price']) ? $data['base_price'] : 0.00;
        $rFare->first_distance = isset($data['first_distance']) ? $data['first_distance'] : 0;
        $rFare->first_distance_price = isset($data['first_distance_price']) ? $data['first_distance_price'] : 0.00;
        $rFare->after_first_distance_price = isset($data['after_first_distance_price']) ? $data['after_first_distance_price'] : 0.00;
        $rFare->wait_time_price = isset($data['wait_time_price']) ? $data['wait_time_price'] : 0.00;
        $rFare->cancellation_fee = isset($data['cancellation_fee']) ? $data['cancellation_fee'] : 0.00;

        $rFare->save();

        /** update fare in cache */
        Cache::forever(RideFare::serviceFareCacheKey($vehicleTypeId), $rFare);

        return $rFare;

    }

}