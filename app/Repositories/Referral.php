<?php

/**
 * @author Saikat Dutta
 * @description used to handle all referral process
 */

namespace App\Repositories;

use App\Models\Referral\ReferralCode;
use App\Models\Referral\ReferralHistory;
use App\Models\Setting;
use Illuminate\Support\Str;

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




    /**
     * generate and return referral code 32char length
     */
    public function generateReferralCode()
    {
        return strtoupper(uniqid(date('YmdHis')));
    }




    /**
     * create and save referral code
     * when user object created and saved 
     * it will be called and create referral code and with 0 bonus amount will be saved
     */
    public function createReferralCodeEntry($etype, $eid, $bAmount = 0)
    {
        $referralCode = new $this->referralCode;
        $referralCode->code = $this->generateReferralCode();
        $referralCode->e_type = $etype;
        $referralCode->e_id = $eid;
        $referralCode->bonus_amount = $bAmount;
        $referralCode->status = ReferralCode::ENABLED;
        $referralCode->save();
        return $referralCode;
    }




    /**
     * create referral code is not exists
     */
    public function createReferralCodeIfNotExists($etype, $eid, $bAmount = 0)
    {
        //fetch referral code record by type and id 
        $referralCode = $this->referralCode->where('e_type', $etype)->where('e_id', $eid)->first();

        //if not exists then create
        if(!$referralCode) {
            $referralCode = $this->createReferralCodeEntry($etype, $eid, $bAmount);
        }

        return $referralCode;

    }




}
