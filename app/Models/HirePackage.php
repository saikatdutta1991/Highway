<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Repositories\Utill;

class HirePackage extends Model
{

    protected $table = 'driver_hiring_packages';
    protected $appends = ["night_charge_start", "night_charge_end"];

    public static function table()
    {
        return "driver_hiring_packages";
    }


    public function getNightChargeStartAttribute()
    {
        return $this->night_hours == "" ? "" : Utill::getHoursList()[ explode("-", $this->night_hours)[0] ];
    }

    public function getNightChargeEndAttribute()
    {
        return $this->night_hours == "" ? "" : Utill::getHoursList()[ explode("-", $this->night_hours)[1] ];
    }

}