<?php

namespace App\Models\Referral\Observers;

use App\Models\User;
use App\Repositories\Referral;
use App\Repositories\Api;

class UserObserver
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, Referral $referral)
    {
        $this->api = $api;
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
        //when user created, make referral code entry 
        $this->referral->createReferralCodeEntry('user', $user->id);

        //if request referral_code is there, create referral history entry
        if(request()->has('referral_code')) {
            $this->referral->makeReferred('user', request()->referral_code, $user->id);
        }
        
    }

    
}