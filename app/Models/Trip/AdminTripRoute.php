<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Model;

class AdminTripRoute extends Model
{

    protected $table = 'admin_trip_routes';

    public function getTableName()
    {
        return $this->table;
    }

}