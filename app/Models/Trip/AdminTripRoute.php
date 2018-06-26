<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Model;

class AdminTripRoute extends Model
{

    //constant properties
    const ENABLED = 'ENABLED';
    const DISABLED = 'DISABLED';

    protected $table = 'admin_trip_routes';

    public function getTableName()
    {
        return $this->table;
    }


    /**
     * relation with from location
     */
    public function from()
    {
        return $this->belongsTo('App\Models\Trip\AdminTripLocation', 'from_location');
    }


    /**
     * relation with from location
     */
    public function to()
    {
        return $this->belongsTo('App\Models\Trip\AdminTripLocation', 'to_location');
    }


    /**
     * formated created at
     */
    public function createdOn($timezone)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->setTimezone($timezone)->format('d-m-Y h:i A');
    }

}