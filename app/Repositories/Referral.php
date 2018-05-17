<?php

/**
 * @author Saikat Dutta
 * @description used to handle all referral process
 */

namespace App\Repositories;

use App\Models\Referral\ReferralCode;
use App\Models\Referral\ReferralHistory;
use App\Models\Setting;

class referral
{

    /**
     * init dependencies
     */
    public function __construct(Setting $setting, ReferralCode $referralCode, ReferralHistory $referralHistory)
    {
        $this->setting = $setting;
        $this->referralCode = $referralCode;
        $this->referralHistory = $referralHistory;
    }



    /**
     * check referral module enable or not
     * return true or false
     */
    public function isEnabled()
    {
        $referralSet = $this->setting->get('referral_module_enabled');
        return $referralSet === 'true';
    }



    /**
     * returns default referrer bonus amount
     */
    public function referrerBonusAmount()
    {
        $str = $this->setting->get('referrer_bonus_amount');
        return $str ? intval($str) : 0; 
    }


    /**
     * returns default referred bonus amount
     */
    public function referredBonusAmount()
    {
        $str = $this->setting->get('referred_bonus_amount');
        return $str ? intval($str) : 0; 
    }



}
