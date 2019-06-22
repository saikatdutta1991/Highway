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

}