<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Referral as ReferralRepo;
use App\Repositories\Utill;
use App\Models\Setting;

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
            $rcRow = $this->referral->createReferralCodeIfNotExists('user', $request->auth_user->id);
            $referralCode = $rcRow->code;
            $referralBonusAmount = $rcRow->bonus_amount;
        }

        $share_url = route('referrals.redirect', ['referrer_code' => $referralCode]);
        $refertext = Utill::transMessage('app_messages.user_referral_text', [
            'url' => $share_url, 
            'code' => $referralCode,
            'referrer_amount' => Setting::get('referrer_bonus_amount'),
            'referee_amount' => Setting::get('referred_bonus_amount'),
            'currency_symbol' => Setting::get('currency_symbol')
        ]);


        return $this->api->json(true, 'REFERRAL_INFO', 'Referral info fetched', [
            'code' => $referralCode,
            'bonus_amount' => $referralBonusAmount,
            'module_enabled' => $this->referral->isEnabled(),
            'share_url' => $share_url,
            'refer_text' => $refertext
        ]);

        
    }




    /**
     * verify referral code
     */
    public function verifyReferralCode(Request $request)
    {

        $valid = $this->referral->isReferralCodeValid('user', $request->code);
        return $this->api->json($valid, 'REFERRAL_VALIDITY_CHECK', 'Referral validity check', [
            'code' => $request->code,
            'is_valid' => $valid,
            'module_enabled' => $this->referral->isEnabled()
        ]);

    }




}
