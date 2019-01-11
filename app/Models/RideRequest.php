<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideRequest extends Model
{

    protected $table = 'ride_requests';

    const CASH = 'CASH'; //default payment mode
    const ONLINE = 'ONLINE';
    const COD = 'COD';
    protected $paymentModes = [self::CASH, self::ONLINE];

    /**
     * payment status list
     */
    const NOT_PAID = 'NOT_PAID';
    const PAID = 'PAID';


    /**
     * rating array const
     */
    const RATINGS = [1, 2, 3, 4, 5];


    /**
     * ride request status list
     */
    const INITIATED = 'INITIATED'; //when first ride request is created

     //when user canceled ride request. user cannot cancel request after driver has accepted request 
    const USER_CANCELED = 'USER_CANCELED';
    
    //when driver canceled ride request. driver can cancel request any time.
    const DRIVER_CANCELED = 'DRIVER_CANCELED';

    //when driver accepts the request
    const DRIVER_ACCEPTED = 'DRIVER_ACCEPTED';

    //when driver starts to go to user
    const DRIVER_STARTED = 'DRIVER_STARTED';

    //when driver reached to the user
    const DRIVER_REACHED = 'DRIVER_REACHED';

    //when driver starts the trip
    const TRIP_STARTED = 'TRIP_STARTED';

    //when driver ends the trip
    const TRIP_ENDED = 'TRIP_ENDED';
    
    /**
     * when trip completes
     * trip completes after both user and driver has given rating and payment is made already
     */
    const COMPLETED = 'COMPLETED';



    /**
     * get table name statically
     */
    public static function table()
    {
        return 'ride_requests';
    }


    public function getTableName()
    {
        return $this->table;
    }


    /**
     * returns payment modes
     */
    public function getPaymentModes()
    {
        return $this->paymentModes;
    }


    /**
     * retusn allowd request status when payment mode can be updated
     */
    public function updatePaymentModeAllowedStatusList()
    {
        return [self::INITIATED];
    }



    /**
     * returns allowed request status when request can be canceled by user
     * only before driver strated the trip
     */
    public function rideRequestCancelAllowedStatusList()
    {
        return [self::INITIATED, self::DRIVER_ACCEPTED, self::DRIVER_STARTED, self::DRIVER_REACHED];
    }



    /**
     * returns allowed request status when request can be canceled by driver
     * only after request accepted driver and before trip ended
     */
    public function driverRideRequestCancelAllowedStatusList()
    {
        return [self::DRIVER_ACCEPTED, self::DRIVER_STARTED, self::DRIVER_REACHED, self::TRIP_STARTED];
    }




    /** 
     * returns ride request status list when ride is not ongoing
     */
    public function notOngoigRideRequestStatusList()
    {
        return [self::INITIATED, self::COMPLETED, self::USER_CANCELED, self::DRIVER_CANCELED];
    }


    /** 
     * returns ride request status list when ride is not ongoing for driver
     */
    public function notOngoigRideRequestStatusListDriver()
    {
        return [self::INITIATED, self::COMPLETED, self::USER_CANCELED, self::DRIVER_CANCELED, self::TRIP_ENDED];
    }



   

    /**
     *  relatitionship with user
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
        return $this->belongsTo('App\Models\RideRequestInvoice', 'ride_invoice_id');
    }




    /**
     * relationship with driver
     */
    public function driver()
    {
        return $this->belongsTo('App\Models\Driver', 'driver_id');
    }


    /**
     * ride request start time
     */
    public function getStartTime($timezone)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->ride_start_time)->setTimezone($timezone)->format('h:i a');
    }

    /**
     * ride request start time
     */
    public function getEndTime($timezone)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->ride_end_time)->setTimezone($timezone)->format('h:i a');
    }




    /** 
     * calculate driver rating for ride requests
    */
    public static function getDriverRideRatingDetails($driverId)
    {  
        $selects = [
            'SUM('.self::table().'.driver_rating) AS driver_rating_sum',
            'COUNT('.self::table().'.id) AS ride_request_count'
        ];

        //find total number of requests and sum of total ratings
        $record = self::where('driver_id', $driverId)
            ->whereIn('driver_rating', self::RATINGS)
            ->selectRaw(implode(',', $selects))->first();
        
        return [(integer)$record->driver_rating_sum, $record->ride_request_count];

    }




    /**
     * rating for this request and 
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
            'COUNT('.$this->getTableName().'.id) AS ride_request_count'
        ];

        //find total number of requests and sum of total ratings
        $rating = $this->where('user_id', $this->user->id)->whereIn('user_rating', self::RATINGS)
        ->selectRaw(implode(',', $selects))->first();

        //increment rating count and sum with new
        $rating->ride_request_count += 1;
        $rating->user_rating_sum = $rating->user_rating_sum + intval($ratingValue);
        
        //calculating rating
        $updatedRating = $rating->user_rating_sum / $rating->ride_request_count;

        return [$ratingValue, $updatedRating];

    }




    /**
     * driver total money payments earned
     */
    public function revenueGenerated($driverId, $paymentModes = null)
    {
        $rritn = app('App\Models\RideRequestInvoice')->getTable();

        $queryBuilder = $this->join($rritn, $this->table.'.ride_invoice_id', '=', $rritn.'.id')
        ->where($this->table.'.driver_id', $driverId)
        ->where($this->table.'.ride_status', self::COMPLETED);

        if(is_array($paymentModes)) {
            $queryBuilder =  $queryBuilder->whereIn($this->table.'.payment_mode', $paymentModes);
        } else if(is_string($paymentModes)) {
            $queryBuilder =  $queryBuilder->where($this->table.'.payment_mode', $paymentModes);
        }

        return $queryBuilder->sum($rritn.'.total');

    }



}