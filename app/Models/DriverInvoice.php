<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverInvoice extends Model
{

    protected $table = 'driver_invoices';

    public static function table()
    {
        return 'driver_invoices';
    }



    /** relationship with ride */
    public function ride()
    {
        if($this->ride_type == 'city') {
            return $this->belongsTo(\App\Models\RideRequest::class, 'ride_id');
        } else {
            return $this->belongsTo(\App\Models\Trip\Trip::class, 'ride_id');
        }
    }


    /**
     * returns registerd on formater date(created_at)
     */
    public function createdOn($timezone)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->setTimezone($timezone)->format('d M Y . h:m a');
    }



}