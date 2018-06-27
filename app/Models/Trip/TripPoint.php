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


   
}