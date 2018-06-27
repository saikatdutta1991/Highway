<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{

    /**
     * rating array const
     */
    const RATINGS = [1, 2, 3, 4, 5];

    //const values
    const COMPLETED = "COMPLETED";
    const CREATED = "CREATED";
    const TRIP_STARTED = "TRIP_STARTED";
    const TRIP_ENDED = "TRIP_ENDED";
    const TRIP_CANCELED = "TRIP_CANCELED_DRIVER"; //canceled by driver 


    protected $table = 'trips';

    public function getTableName()
    {
        return $this->table;
    }



   
    /**
     * relation with trip points
     */
    public function points()
    {
        return $this->hasMany('App\Models\Trip\TripPoint', 'trip_id');
    }


   
    /**
     * relationship with driver
     */
    public function driver()
    {
        return $this->belongsTo('App\Models\Driver', 'driver_id');
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