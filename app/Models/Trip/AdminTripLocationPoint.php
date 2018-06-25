<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Model;

class AdminTripLocationPoint extends Model
{

    protected $table = 'admin_trip_location_points';

    public function getTableName()
    {
        return $this->table;
    }

}