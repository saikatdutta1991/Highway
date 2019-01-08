<?php

namespace App\Models\Trip;

use Illuminate\Database\Eloquent\Model;

class TripBooking extends Model
{

    /**
     * rating array const
     */
    const RATINGS = [1, 2, 3, 4, 5];

    //const values
    const INITIATED = "INITIATED";
    const BOOKING_CANCELED_USER = "BOOKING_CANCELED_USER"; //canceled by driver 
    const BOOKING_CONFIRMED = 'BOOKING_CONFIRMED';

    protected $table = 'trip_bookings';

    public function getTableName()
    {
        return $this->table;
    }



    /** is user boarded */
    public function isBoarded()
    {
        return !!$this->boarding_time;
    }

    /**
     * is booking canceled 
     */
    public function isBookingCancelled()
    {
        return ($this->booking_status == TripBooking::BOOKING_CANCELED_USER 
        || $this->booking_status == Trip::TRIP_CANCELED_DRIVER);
    }



    /**
     * formated cancel by
     */
    public function formatedCanceledBy()
    {
        if($this->booking_status == TripBooking::BOOKING_CANCELED_USER) {
            return 'User';
        } else if($this->booking_status == Trip::TRIP_CANCELED_DRIVER) {
            return 'Driver';
        }
    }




    /**
     * relation with trip
     */
    public function trip()
    {
        return $this->belongsTo('App\Models\Trip\Trip', 'trip_id');
    }


    /**
     * sources point 
     */
    public function boardingPoint()
    {
        return $this->belongsTo('App\Models\Trip\TripPoint', 'boarding_point_id');
    }


    /**
     * destination point 
     */
    public function destPoint()
    {
        return $this->belongsTo('App\Models\Trip\TripPoint', 'dest_point_id');
    }




    /**
     * relation with user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


    /**
     *  relatitionship with ride_request_invoices
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\RideRequestInvoice', 'invoice_id');
    }



    public function tripFormatedCratedTimestamp()
    {
        $timezone = app('UtillRepo')->getTimezone($this->user->timezone);
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->timezone($timezone);   
        return $date->format('d-m-Y'). $date->format(' h:i A');
    }


    /** 
     * formated booking date time
     */
    public function formatedBookingDate()
    {
        $timezone = app('UtillRepo')->getTimezone($this->user->timezone);
        $date = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->timezone($timezone);   
        return $date->format('D d-M-Y').' at '. $date->format(' h:i A');
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




    /**
     * tracking api
     */
    public function trackBookingUrl()
    {
        return route('track-booking', ['bookingid' => $this->booking_id]);
    }



}