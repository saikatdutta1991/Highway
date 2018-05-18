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
        $this->referral->createReferralCodeEntry('driver', $driver->id);
    }

    
}