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
    const DRIVER_STARTED = "DRIVER_STARTED";
    const TRIP_STARTED = "TRIP_STARTED";
    const TRIP_ENDED = "TRIP_ENDED";
    const TRIP_CANCELED = "TRIP_CANCELED"; //canceled by driver 



    const CASH = 'CASH'; //default payment mode
    const ONLINE = 'ONLINE'; //payu payment mode
    const PAYMENT_MODES = [self::CASH, self::ONLINE];


    /**payment methods */
    const CARD = "CARD";
    const COD = 'COD';


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
    /* public function createDate($dateString)
    {
        //check date if proper
        try {
            $date = \Carbon\Carbon::createFromFormat('Y-m-d', $dateString);
        } catch(\Exception $e) {
            return false;
        }
        
        return $date;
    } */




    /**
     * relation with trip points
     */
    public function tripPoints()
    {
        return $this->hasMany('App\Models\TripPoint', 'trip_id');
    }


    /**
     * relation with trip routes
     */
    public function tripRoutes()
    {
        return $this->hasMany('App\Models\TripRoute', 'trip_id');
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
     * takes points is html form array
     */
    public function rulesPickupPoints($points)
    {
        if(!is_array($points)) {
            return [];
        }
       
        $keyRules = app('App\Models\TripPoint')->keyRules();

        $rules = [];
        foreach($points as $index => $point){
            foreach($keyRules as $key => $rule) {
                $rules["points.{$index}.{$key}"] = $rule;
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
            'trip_date_time' => 'required|date_format:Y-m-d H:i:s',
        ], $this->rulesPickupPoints($request->points));
    }


    /**
     * delete whole trip 
     * checking must be done before like initiated or not
     */
    public function deleteTrip($tripId)
    {
        $this->where('id', $tripId)->forceDelete();
        app('App\Models\TripRoute')->where('trip_id', $tripId)->forceDelete();
        app('App\Models\TripPoint')->where('trip_id', $tripId)->forceDelete();
        return true;
    }



    /**
     * get trip date formated string d-m-Y
     */
    public function tripFormatedDateString()
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->date_time)->timezone($this->driver->timezone);
        return $date->formatLocalized('%d-%m-%Y');
    }


    /**
     * get trip time formated string am pm
     */
    public function tripFormatedTimeString()
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->date_time)->timezone($this->driver->timezone);
        return $date->format('h:i A');
    }


}