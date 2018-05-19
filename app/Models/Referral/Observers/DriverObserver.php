<?php

namespace App\Models\Referral\Observers;

use App\Models\Driver;
use App\Repositories\Referral;

class DriverObserver
{

    /**
     * init dependencies
     */
    public function __construct(Referral $referral)
    {
        $this->referral = $referral;
    }



    /**
     * Listen to the Driver created event.
     *
     * @param  \App\Driver  $driver
     * @return void
     */
    public function created(Driver $driver)
    {
        //when driver created, make referral code entry 
        $this->referral->createReferralCodeEntry('driver', $driver->id);

        //if request referral_code is there, create referral history entry
        if(request()->has('referral_code')) {
            $this->referral->makeReferred('driver', request()->referral_code, $driver->id);
        }

    }

    
}