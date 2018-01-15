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
    public function calculateFare($distance, $avgDuration)
    {
        $utillRepo = app('App\Repositories\Utill');

        $fare = $this->calculateRideFare($distance, $avgDuration);
        $taxes = $this->calculateRideFareTax($fare);
        $accessFee = $this->access_fee;
        $total = $fare + $this->access_fee + $taxes;
        $total = number_format(round($total), 2, '.', '');

        return [
            'ride_fare' => $fare,
            'ride_fare_details' => [
                'base_price' => $this->base_price,
                'first_distance' => $this->first_distance,
                'first_distance_price' => $this->first_distance_price,
                'after_first_distance_price' => $this->after_first_distance_price,
                'wait_time_price' => $this->wait_time_price,
            ],
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
        return round($taxes, 2);
    }





    /**
     * calculate ride fare only
     */
    public function calculateRideFare($distance, $avgDuration)
    {
        $utillRepo = app('App\Repositories\Utill');

        $fare = 0;

        //adding base price
        $fare += $this->base_price;

        //checking first distance price (eg. first 2 km 10$)
        if($distance >= $this->first_distance) {
            $fare += $this->first_distance_price;
            $distance -= $this->first_distance;
        }


        $fare += ($this->after_first_distance_price * $distance);
        $fare += ($avgDuration * $this->wait_time_price);

        //checking if cost is less than minimum price
        $fare = ($fare < $this->minimun_price) ? $this->minimun_price : $fare;
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
        $rFare->after_first_distance_price = isset($data['after_first_distance_price']) ? $data['after_first_distance_price'] : 0.00;
        $rFare->wait_time_price = isset($data['wait_time_price']) ? $data['wait_time_price'] : 0.00;
        $rFare->cancellation_fee = isset($data['cancellation_fee']) ? $data['cancellation_fee'] : 0.00;

        $rFare->save();

        return $rFare;

    }

}