<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRoutePath extends Model
{

    protected $table = 'admin_route_paths';

    public function getTableName()
    {
        return $this->table;
    }


    /**
     * relation with trip
     */
    public function adminRoute()
    {
        return $this->belongsTo('App\Models\AdminRoute', 'admin_route_id');
    }



}