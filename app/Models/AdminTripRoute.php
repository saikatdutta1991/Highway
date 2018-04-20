<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminTripRoute extends Model
{

    protected $table = 'admin_trip_routes';

    public function getTableName()
    {
        return $this->table;
    }



    /**
     * relation with trip
     */
    public function trip()
    {
        return $this->belongsTo('App\Models\AdminTrip', 'admin_trip_id');
    }



    /**
     * calculate and save trip routes seat affects if any route booked
     * eg : A -> B -> C
     * ROUTES : A -> B, B -> C, A -> C each 5 seats available
     * if A -> B 3 seats booked then it will affects A -> C route seats availablity
     * P1 >= Xi && P2 <= Yi
     */
    public function calculateSeatAffects(&$tripRoutes, $save = true)
    {

        foreach($tripRoutes as $index => $outerTripRoute) {
            
            $affects = [];
            
            foreach($tripRoutes as $innerTripRoute) {
                
                $m = $outerTripRoute->start_point_order + $outerTripRoute->end_point_order;
                $l = $innerTripRoute->start_point_order + $outerTripRoute->start_point_order;
                $r = $innerTripRoute->end_point_order + $outerTripRoute->end_point_order;
                if( $l < $m && $m < $r ) {
                    $affects[] = $innerTripRoute->id;
                }
            }

            $tripRoutes[$index]->seat_affects = implode($affects, ',');
            
            if($save) {
                $tripRoutes[$index]->save();
            }
            

        }

        return true;

    }


}