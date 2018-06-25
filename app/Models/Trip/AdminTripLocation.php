<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Model;

class AdminTripLocation extends Model
{

    protected $table = 'admin_trip_locations';

    public function getTableName()
    {
        return $this->table;
    }


    /** 
     * relation with trip location points
     */
    public function points()
    {
        return $this->hasMany('App\Models\Trip\AdminTripLocationPoint', 'admin_trip_location_id');
    }



    /**
     * formated created at
     */
    public function createdOn($timezone)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->setTimezone($timezone)->format('d-m-Y h:i A');
    }


}