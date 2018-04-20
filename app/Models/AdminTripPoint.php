<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminTripPoint extends Model
{

    protected $table = 'admin_trip_points';

    public function getTableName()
    {
        return $this->table;
    }


    //relation with trip
    public function trip()
    {
        return $this->belongsTo('App\Models\AdminTrip', 'admin_trip_id');
    }

    //relation with driver
    public function driver()
    {
        return $this->trip()->driver();
    }


    /**
     * trip pickup point key rules
     */
    public function keyRules()
    {
        list($latRegex, $longRegex) = app('UtillRepo')->regexLatLongValidate();
        return $keyRules = [
            'address' => 'required|min:1|max:256', 
            'latitude' => ['required', 'regex:'.$latRegex], 
            'longitude' => ['required', 'regex:'.$longRegex],
            'distance' => 'sometimes|required',
            'time' => 'sometimes|required',
            'fare' => 'sometimes|required'
        ];
    }


}