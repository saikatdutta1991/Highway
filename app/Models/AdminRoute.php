<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminRoute extends Model
{

    /**
     * route status constants
     */
    const ENABLED = 'ENABLED';

    protected $table = 'admin_routes';

    public function getTableName()
    {
        return $this->table;
    }


    /**
     * relation with admin route points
     */
    public function points()
    {
        return $this->hasMany('App\Models\AdminRoutePoint', 'admin_route_id');
    }


    /**
     * returns validation rules for trip create input other pickup points
     * takes points is html form array
     */
    public function rulesPickupPoints($points)
    {
        if(!is_array($points)) {
            return [];
        }
       
        $keyRules = app('App\Models\AdminRoutePoint')->keyRules();

        $rules = [];
        foreach($points as $index => $point){
            foreach($keyRules as $key => $rule) {
                $rules["points.{$index}.{$key}"] = $rule;
            }
        }

        return $rules;
    }



    /**
     * create trip validation rules
     * takes laravel request object
     */
    public function createTripValidationRules($request)
    {
        list($latRegex, $longRegex) = app('UtillRepo')->regexLatLongValidate();
        return array_merge([
            'name' => 'required|max:256|unique:'.$this->getTableName().',name',
        ], $this->rulesPickupPoints($request->points));
    }


    /**
     * get formated created at
     */
    public function formatedCreatedAt($timezone = 'Asia\Kolkata')
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->setTimezone($timezone)->format('d M, Y');
    }


}