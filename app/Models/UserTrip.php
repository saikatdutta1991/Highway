<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Trip;

class UserTrip extends Model
{


    /**
     * rating array const
     */
    const RATINGS = [1, 2, 3, 4, 5];

    const USER_CANCELED = 'USER_CANCELED';


    protected $table = 'users_trip_bookings';

    public function getTableName()
    {
        return $this->table;
    }



    /**
     * relation with trip route
     */
    public function tripRoute()
    {
        return $this->belongsTo('App\Models\TripRoute', 'trip_route_id');
    }


    /**
     * relation with trip
     */
    public function trip()
    {
        return $this->belongsTo('App\Models\Trip', 'trip_id');
    }



    /**
     * relation with user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


    /**
     * relation with invoice (ride request invoice)
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\RideRequestInvoice', 'trip_invoice_id');
    }
     

    /**
     * rating for this booking and 
     * calculate user rating and ride request rating
     */
    public function calculateUserRating($ratingValue)
    {   
        
        //check if already rating given or not
        if( in_array($this->user_rating, self::RATINGS)) {
            return [$this->user_rating, $this->user->rating];
        }


        $selects = [
            'SUM('.$this->getTableName().'.user_rating) AS user_rating_sum',
            'COUNT('.$this->getTableName().'.id) AS booking_request_count'
        ];

        //find total number of requests and sum of total ratings
        $rating =$this->where('user_id', $this->user->id)->whereIn('user_rating', self::RATINGS)
        ->selectRaw(implode(',', $selects))->first();

        //increment rating count and sum with new
        $rating->booking_request_count += 1;
        $rating->user_rating_sum = $rating->user_rating_sum + intval($ratingValue);
        
        //calculating rating
        $updatedRating = $rating->user_rating_sum / $rating->booking_request_count;

        return [$ratingValue, $updatedRating];

    }



    /**
     * rating for this booking and 
     * calculate driver rating and ride request rating
     */
    public function calculateDriverRating($ratingValue)
    {   
        
        //check if already rating given or not
        if( in_array($this->driver_rating, self::RATINGS)) {
            return [$this->driver_rating, $this->trip->driver->rating];
        }


        $selects = [
            'SUM('.$this->getTableName().'.driver_rating) AS driver_rating_sum',
            'COUNT('.$this->getTableName().'.id) AS trip_request_count'
        ];

        //find total number of requests and sum of total ratings
        $rating = $this->join('trips', 'trips.id', '=', $this->getTableName().'.trip_id')
        ->where('trips.driver_id', $this->trip->driver->id)
        ->whereIn($this->getTableName().'.driver_rating', self::RATINGS)
        ->selectRaw(implode(',', $selects))->first();

        //increment rating count and sum with new
        $rating->trip_request_count += 1;
        $rating->driver_rating_sum = $rating->driver_rating_sum + intval($ratingValue);
        
        //calculating rating
        $updatedRating = $rating->driver_rating_sum / $rating->trip_request_count;

        return [$ratingValue, $updatedRating];

    }




}