<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HirePackage extends Model
{

    protected $table = 'driver_hiring_packages';

    public static function table()
    {
        return "driver_hiring_packages";
    }

}