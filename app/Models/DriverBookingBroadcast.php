<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverBookingBroadcast extends Model
{

    protected $table = 'driver_booking_broadcasts';

    //status can be
    //pending
    //accepted
    //rejected

    public static function table()
    {
        return "driver_booking_broadcasts";
    }

}