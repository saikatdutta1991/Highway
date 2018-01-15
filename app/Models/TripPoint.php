<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TripPoint extends Model
{

    protected $table = 'trip_points';

    public function getTableName()
    {
        return $this->table;
    }


    //relation with trip
    public function trip()
    {
        return $this->belongsTo('App\Models\Trip', 'trip_id');
    }

    //relation with driver
    public function driver()
    {
        return $this->trip()->driver();
    }


    /**
     * all user bookings
     */
    public function userBookings()
    {
        return $this->hasMany('App\Models\UserTrip', 'trip_point_id');
    }


    /**
     * trip pickup point key rules
     */
    public function keyRules()
    {
        list($latRegex, $longRegex) = app('UtillRepo')->regexLatLongValidate();
        return $keyRules = [
            'estimated_trip_distance' => 'required|numeric', //km
            'estimated_trip_time' => 'required|numeric', //minute
            'address' => 'required|min:1|max:256', 
            'latitude' => ['required', 'regex:'.$latRegex], 
            'longitude' => ['required', 'regex:'.$longRegex]
        ];
    }


}