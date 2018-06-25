<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Model;

class AdminTripLocation extends Model
{

    protected $table = 'admin_trip_locations';

    public function getTableName()
    {
        return $this->table;
    }

}