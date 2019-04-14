<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverBank extends Model
{

    protected $table = 'driver_banks';
    
    public static function table()
    {
        return 'driver_banks';
    }

}