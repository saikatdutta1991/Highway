<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTrip extends Model
{

    const USER_CANCELED = 'USER_CANCELED';


    protected $table = 'users_trip_bookings';

    public function getTableName()
    {
        return $this->table;
    }



    /**
     * relation with trip route
     */
    public function tripRoute()
    {
        return $this->belongsTo('App\Models\TripRoute', 'trip_route_id');
    }


    /**
     * relation with trip
     */
    public function trip()
    {
        return $this->belongsTo('App\Models\Trip', 'trip_id');
    }



    /**
     * relation with user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


    /**
     * relation with invoice (ride request invoice)
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\RideRequestInvoice', 'trip_invoice_id');
    }


}