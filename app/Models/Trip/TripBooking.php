<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Model;

class TripBooking extends Model
{

    /**
     * rating array const
     */
    const RATINGS = [1, 2, 3, 4, 5];

    //const values
    const INITIATED = "INITIATED";
    const BOOKING_CANCELED_USER = "BOOKING_CANCELED_USER"; //canceled by driver 
    const BOOKING_CONFIRMED = 'BOOKING_CONFIRMED';

    protected $table = 'trip_bookings';

    public function getTableName()
    {
        return $this->table;
    }


    /**
     * relation with trip
     */
    public function trip()
    {
        return $this->belongsTo('App\Models\Trip\Trip', 'trip_id');
    }


    /**
     * sources point 
     */
    public function boardingPoint()
    {
        return $this->belongsTo('App\Models\Trip\TripPoint', 'boarding_point_id');
    }


    /**
     * destination point 
     */
    public function destPoint()
    {
        return $this->belongsTo('App\Models\Trip\TripPoint', 'dest_point_id');
    }




    /**
     * relation with user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


    /**
     *  relatitionship with ride_request_invoices
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\RideRequestInvoice', 'invoice_id');
    }


}