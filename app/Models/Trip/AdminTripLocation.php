<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Model;

class AdminTripLocation extends Model
{

    protected $table = 'admin_trip_locations';

    const SOURCE = "source";
    const DESTINATION = "destination";

    public function getTableName()
    {
        return $this->table;
    }

    public function getLocationType() 
    {
        return $this->is_pickup ? ucfirst( self::SOURCE ) : ucfirst( self::DESTINATION );
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