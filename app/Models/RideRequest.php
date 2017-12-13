<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RideRequest extends Model
{

    protected $table = 'ride_requests';

    const CASH = 'CASH'; //default payment mode
    const DEBIT_OR_CREDIT_CARD = 'DEBIT_OR_CREDIT_CARD';
    protected $paymentModes = [self::CASH, self::DEBIT_OR_CREDIT_CARD];


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
     *  relatitionship with user
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }


    /**
     * relationship with driver
     */
    public function driver()
    {
        return $this->belongsTo('App\Models\Driver', 'driver_id');
    }


}