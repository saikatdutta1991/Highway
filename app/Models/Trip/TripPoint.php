<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Model;

class TripPoint extends Model
{


    const CREATED = 'CREATED';
    const DRIVER_STARTED = 'DRIVER_STARTED';
    const DRIVER_REACHED = 'DRIVER_REACHED';

    protected $table = 'trip_points';

    public function getTableName()
    {
        return $this->table;
    }



    /** check if driver started for this point */
    public function isDriverStarted()
    {
        return $this->status == self::DRIVER_STARTED || $this->status == self::DRIVER_REACHED;
    }



    /**
     * is driver reached the point
     */
    public function isDriverReached()
    {
        return $this->status == self::DRIVER_REACHED;
    }



    //relation with trip
    public function trip()
    {
        return $this->belongsTo('App\Models\Trip\Trip', 'trip_id');
    }

    //relation with driver
    public function driver()
    {
        return $this->trip()->driver();
    }



    /**
     * relation with booking boaring point
     */
    public function boardingBookings()
    {
        return $this->hasMany('App\Models\Trip\TripBooking', 'boarding_point_id');
    }

    /**
     * relation with booking dest point
     */
    public function destBookings()
    {
        return $this->hasMany('App\Models\Trip\TripBooking', 'dest_point_id');
    }


   
}