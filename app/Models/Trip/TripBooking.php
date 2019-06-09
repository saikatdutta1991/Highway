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

    /**
     * get table name statically
     */
    public static function table()
    {
        return 'trip_bookings';
    }

    public static function tablename()
    {
        return 'trip_bookings';
    }

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
     * calculate driver rating for trips bookings
    */
    public static function getUserRideRatingDetails($userId)
    {   
        $selects = [
            'SUM('.self::table().'.user_rating) AS user_rating_sum',
            'COUNT('.self::table().'.id) AS trip_request_count'
        ];

        //find total number of requests and sum of total ratings
        $record = self::where('user_id', $userId)
            ->whereIn(self::table().'.user_rating', self::RATINGS)
            ->selectRaw(implode(',', $selects))->first();
        

        return [(integer)$record->user_rating_sum, $record->trip_request_count];

    }


    
    /** 
     * calculate driver rating for trips bookings
    */
    public static function getDriverRideRatingDetails($driverId)
    {   
        $selects = [
            'SUM('.self::table().'.driver_rating) AS driver_rating_sum',
            'COUNT('.self::table().'.id) AS trip_request_count'
        ];

        //find total number of requests and sum of total ratings
        $record = self::join('trips', 'trips.id', '=', self::table().'.trip_id')
        ->where('trips.driver_id', $driverId)
        ->whereIn(self::table().'.driver_rating', self::RATINGS)
        ->selectRaw(implode(',', $selects))->first();
        

        return [(integer)$record->driver_rating_sum, $record->trip_request_count];

    }




    /**
     * tracking api
     */
    public function trackBookingUrl()
    {
        return route('track-booking', ['bookingid' => $this->booking_id]);
    }


    /** 
     * source point or boarding point map track link
     */
    public function boardingPointTrackUrl()
    {
        return route('bookings.track.boarding-point-route', ['bookingid' => $this->booking_id]);
    }



}