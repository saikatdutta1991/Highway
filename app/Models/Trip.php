<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{

    //const values
    const INITIATED = "INITIATED";
    const COMPLETED = "COMPLETED";
    const BOOKED = "BOOKED";
    const DRIVER_REACHED = "DRIVER_REACHED";
    const TRIP_STARTED = "TRIP_STARTED";
    const TRIP_CANCELED = "TRIP_CANCELED"; //canceled by driver 



    const CASH = 'CASH'; //default payment mode
    const PAYU = 'PAYU'; //payu payment mode
    const PAYMENT_MODES = [self::CASH, self::PAYU];

    /**
     * payment status list
     */
    const NOT_PAID = 'NOT_PAID';
    const PAID = 'PAID';


    protected $table = 'trips';

    public function getTableName()
    {
        return $this->table;
    }



    /**
     * parse date from string format (Y-m-d)
     * returns carbon date if invalid then false
     */
    public function createDate($dateString)
    {
        //check date if proper
        try {
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $dateString);
        } catch(\Exception $e) {
            return false;
        }
        
        return $date;
    }




    /**
     * relationship with driver
     */
    public function driver()
    {
        return $this->belongsTo('App\Models\Driver', 'driver_id');
    }
    
    


    /**
     * returns validation rules for trip create input other pickup points
     * takes otherPickupPoints is html form array
     */
    public function rulesPickupPoints($otherPickupPoints)
    {
        if(!is_array($otherPickupPoints)) {
            return [];
        }
            
        $keyRules = app('App\Models\TripPoint')->keyRules();

        $rules = [];
        foreach($otherPickupPoints as $index => $pickupPointArray){
            foreach($pickupPointArray as $key => $value) {
                $rules["other_pickup_points.{$index}.{$key}"] = $keyRules[$key];
            }
        }

        return $rules;
    }



    /**
     * create trip validation rules
     * takes laravel request object
     */
    public function createTripValidationRules($request)
    {
        list($latRegex, $longRegex) = app('UtillRepo')->regexLatLongValidate();
        return array_merge([
            'trip_name' => 'required|max:256',
            'seats' => 'required|numeric',
            'estimated_trip_distance' => 'required|numeric', //km
            'estimated_trip_time' => 'required|numeric', //minute
            'trip_date_time' => 'required|date_format:Y-m-d H:i:s',
            'source_address' => 'required|min:1|max:256', 
            'source_latitude' => ['required', 'regex:'.$latRegex], 
            'source_longitude' => ['required', 'regex:'.$longRegex], 
            'destination_address' => 'required|min:1|max:256', 
            'destination_latitude' => ['required', 'regex:'.$latRegex], 
            'destination_longitude' => ['required', 'regex:'.$longRegex]
        ], $this->rulesPickupPoints($request->other_pickup_points));
    }





    /***
     * picup points (relation with trip_points table)
     */
    public function pickupPoints()
    {
        return $this->hasMany('App\Models\TripPoint', 'trip_id');
    }



    /**
     * delete whole trip 
     * checking must be done before like initiated or not
     */
    public function deleteTrip($tripId)
    {
        $this->where('id', $tripId)->forceDelete();
        app('App\Models\TripPoint')->where('trip_id', $tripId)->forceDelete();
        return true;
    }



    /**
     * get trip date formated string d-m-Y
     */
    public function tripFormatedDateString()
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->trip_date_time)->timezone($this->driver->timezone);
        return $date->formatLocalized('%d-%m-%Y');
    }


    /**
     * get trip time formated string am pm
     */
    public function tripFormatedTimeString()
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->trip_date_time)->timezone($this->driver->timezone);
        return $date->format('h:i A');
    }


}