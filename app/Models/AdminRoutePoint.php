<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRoutePoint extends Model
{

    protected $table = 'admin_route_points';

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


    /**
     * route pickup point key rules
     */
    public function keyRules()
    {
        list($latRegex, $longRegex) = app('UtillRepo')->regexLatLongValidate();
        return $keyRules = [
            'address' => 'required|min:1|max:256', 
            'latitude' => ['required', 'regex:'.$latRegex], 
            'longitude' => ['required', 'regex:'.$longRegex],
            'city' => 'required',          
            'country' => 'required',          
            'zip_code' => 'required',          
        ];
    }


}