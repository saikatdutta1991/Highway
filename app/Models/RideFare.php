<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideFare extends Model
{

    protected $table = 'ride_fares';

    public function getTableName()
    {
        return $this->table;
    }



    /**
     * calculate/estimate price
     * distance in km(s)
     * $avgDuration in minute(s)
     * call this method if fare object fetched
     */
    public function calculateFare($rFare, $distance, $avgDuration)
    {
        $fare = $this->calculateRideFare($rFare, $distance, $avgDuration);
        $taxes = $this->calculateRideFareTax($fare);
        $accessFee = $rFare->access_fee;
        $total = $fare + $rFare->access_fee + $taxes;
        
        //un setgings unnecessary properties
        unset($rFare->access_fee);
        unset($rFare->id);
        unset($rFare->vehicle_type_id);
        unset($rFare->created_at);
        unset($rFare->updated_at);
        unset($rFare->deleted_at);
        unset($rFare->cancellation_fee);

        return [
            'ride_fare' => $fare,
            'ride_fare_details' => $rFare->toArray(),
            'access_fee' => $accessFee,
            'taxes' => $taxes,
            'total' => $total
        ];
     
    }



    /**
     * calculate only fare tax
     */
    public function calculateRideFareTax($fare)
    {
        $utillRepo = app('App\Repositories\Utill');

        //calculate taxes
        $taxPercentage = $tax = app('App\Models\Setting')->get('vehicle_ride_fare_tax_percentage');
        $taxPercentage = $taxPercentage == '' ? 0 : $taxPercentage;
        $taxes = $fare * ( $taxPercentage / 100 );
        $taxes = $utillRepo->formatAmountDecimalTwo($taxes);
        return $taxes;
    }





    /**
     * calculate ride fare only
     */
    public function calculateRideFare($rFare, $distance, $avgDuration)
    {
        $utillRepo = app('App\Repositories\Utill');

        $fare = 0;

        //adding base price
        $fare += $rFare->base_price;

        //checking first distance price (eg. first 2 km 10$)
        if($distance >= $rFare->first_distance) {
            $fare += $rFare->first_distance;
            $distance -= $rFare->first_distance;
        }


        $fare += ($rFare->after_first_distance_price * $distance);
        $fare += ($avgDuration * $rFare->wait_time_price);

        //checking if cost is less than minimum price
        $fare = ($fare < $rFare->minimum_price) ? $rFare->minimum_price : $fare;
        $fare = $utillRepo->formatAmountDecimalTwo($fare);

        return $fare;
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
        $rFare->after_first_distance_price = isset($data['after_first_distance_price']) ? $data['after_first_distance_price_per_km'] : 0.00;
        $rFare->wait_time_price = isset($data['wait_time_price']) ? $data['wait_time_price'] : 0.00;
        $rFare->cancellation_fee = isset($data['cancellation_fee']) ? $data['cancellation_fee'] : 0.00;

        $rFare->save();

        return $rFare;

    }

}