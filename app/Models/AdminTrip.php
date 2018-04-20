<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminTrip extends Model
{

    protected $table = 'admin_trips';

    public function getTableName()
    {
        return $this->table;
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
       
        $keyRules = app('App\Models\AdminTripPoint')->keyRules();

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
            'name' => 'required|max:256',
            /* 'no_of_seats' => 'required|numeric' */
        ], $this->rulesPickupPoints($request->points));
    }


    /**
     * delete whole trip 
     * checking must be done before like initiated or not
     */
    public function deleteTrip($tripId)
    {
        $this->where('id', $tripId)->forceDelete();
        app('App\Models\TripRoute')->where('trip_id', $tripId)->forceDelete();
        app('App\Models\TripPoint')->where('trip_id', $tripId)->forceDelete();
        return true;
    }






}