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
use App\Repositories\Api;
use DB;

class referral
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, Setting $setting, ReferralCode $referralCode, ReferralHistory $referralHistory)
    {
        $this->api = $api;
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



    /**
     * is referral code valid
     */
    public function isReferralCodeValid($etype, $code, &$referralCode = null)
    {
        $referralCode = $this->referralCode
        ->where('e_type', $etype)
        ->where('code', $code)
        ->where('status', ReferralCode::ENABLED)
        ->first();

        return !!$referralCode;
    }




    /**
     * referrer user or driver 
     * this will create entry to referral history
     */
    public function makeReferred($etype, $rCode, $referredId)
    {
        $referralCode = null;
        if(!$this->isReferralCodeValid($etype, $rCode, $referralCode)) {
            return false;
        }

        $referralHistory = $this->referralHistory->where('referrer_type', $etype)
        ->where('referred_type', $etype)
        ->where('referrer_id', $referralCode->e_id)
        ->where('referred_id', $referredId)
        ->first();

        //if referral history alredy exist
        if($referralHistory) {
            return $referralHistory;
        }

        
        //create entry into referral_histories table
        $referralHistory = new $this->referralHistory;
        $referralHistory->referrer_type = $etype;
        $referralHistory->referrer_id = $referralCode->e_id;
        $referralHistory->referrer_bonus_amount = $this->referrerBonusAmount();
        $referralHistory->referred_type = $etype;
        $referralHistory->referred_id = $referredId;
        $referralHistory->referred_bonus_amount = $this->referredBonusAmount();


        try {

            DB::beginTransaction();

            $referralHistory->save();

            //add bonus amount to users
            $row = $this->referralCode::where('e_type', $etype)
            ->where('e_id', $referralCode->e_id)
            ->first();
            $row->bonus_amount += $this->referrerBonusAmount();
            $row->save();

            $row = $this->referralCode::where('e_type', $etype)
            ->where('e_id', $referredId)
            ->first();
            $row->bonus_amount += $this->referredBonusAmount();
            $row->save();
            

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('MAKE REFERRED', $e->getMessage());
            return false;
        }


        return $referralHistory;


    }



}
