<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTrip extends Model
{

    protected $table = 'users_trip_bookings';

    public function getTableName()
    {
        return $this->table;
    }

}