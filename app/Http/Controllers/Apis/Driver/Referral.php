<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Referral as ReferralRepo;

class Referral extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, ReferralRepo $referral)
    {
        $this->referral = $referral;
        $this->api = $api;
    }



    /**
     * returns referral info
     * if user referral code is not generated then create new
     */
    public function getReferralInfo(Request $request)
    {
        $referralCode = '';
        $referralBonusAmount = 0;

        //if enabled then create and retrive
        if($this->referral->isEnabled()) {
            $rcRow = $this->referral->createReferralCodeIfNotExists('driver', $request->auth_driver->id);
            $referralCode = $rcRow->code;
            $referralBonusAmount = $rcRow->bonus_amount;
        }


        return $this->api->json(true, 'REFERRAL_INFO', 'Referral info fetched', [
            'code' => $referralCode,
            'bonus_amount' => $referralBonusAmount,
            'module_enabled' => $this->referral->isEnabled()
        ]);

        
    }




    /**
     * verify referral code
     */
    public function verifyReferralCode(Request $request)
    {

        $valid = $this->referral->isReferralCodeValid('driver', $request->code);
        return $this->api->json($valid, 'REFERRAL_VALIDITY_CHECK', 'Referral validity check', [
            'code' => $request->code,
            'is_valid' => $valid,
            'module_enabled' => $this->referral->isEnabled()
        ]);

    }




}
