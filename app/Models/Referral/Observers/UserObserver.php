<?php

namespace App\Models\Referral\Observers;

use App\Models\User;
use App\Repositories\Referral;

class UserObserver
{

    /**
     * init dependencies
     */
    public function __construct(Referral $referral)
    {
        $this->referral = $referral;
    }



    /**
     * Listen to the User created event.
     *
     * @param  \App\User  $user
     * @return void
     */
    public function created(User $user)
    {
        $this->referral->createReferralCodeEntry('user', $user->id);
    }

    
}