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
    const TRIP_CANCELED_DRIVER = "TRIP_CANCELED_DRIVER"; //canceled by driver 
    const ONLINE = "ONLINE";
    const PAYMENT_MODES = ['ONLINE'];
    const NOT_PAID = 'NOT_PAID';
    const PAID = 'PAID';
    const FULL_REFUNDED = 'FULL_REFUNDED';
    const PARTIAL_REFUNDED = 'PARTIAL_REFUNDED';

    protected $table = 'trips';

    public function getTableName()
    {
        return $this->table;
    }

    public static function tablename()
    {
        return 'trips';
    }

    public static function table()
    {
        return 'trips';
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
    public function tripFormatedDateString($timezone = null)
    {
        $timezone = $timezone ?: $this->driver->timezone;
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->trip_datetime)->timezone($timezone);
        return $date->formatLocalized('%d-%m-%Y');
    }



    /** trip formated timestamp */
    public function tripFormatedTimestampString($timezone = 'Asia/Kolkata')
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->trip_datetime)->timezone($timezone);
        return $date->formatLocalized('%d-%m-%Y').' '.$date->format('h:i A');
    }


    /**
     * formated journey date time
     */
    public function formatedJourneyDate($timezone = 'Asia/Kolkata')
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->trip_datetime)->timezone($timezone);
        return $date->format('D d-M-Y').' at '.$date->format('h:i A');
    }



    /**
     * get trip time formated string am pm
     */
    public function tripFormatedTimeString()
    {
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->trip_datetime)->timezone($this->driver->timezone);
        return $date->format('h:i A');
    }



    /**
     * relation with admin route
     */
    public function adminRoute()
    {
        return $this->belongsTo('App\Models\Trip\AdminTripRoute', 'admin_route_ref_id');
    }



    /**
     * bookings
     */
    public function bookings()
    {
        return $this->hasMany('App\Models\Trip\TripBooking', 'trip_id');
    } 


}