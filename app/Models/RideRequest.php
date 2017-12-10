<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideRequest extends Model
{

    protected $table = 'ride_requests';

    public function getTableName()
    {
        return $this->table;
    }

}