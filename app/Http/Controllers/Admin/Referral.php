<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting as Set;
use App\Repositories\Referral as ReferralRepo;
use Validator;


class Referral extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(User $user, ReferralRepo $referral, Set $setting, Api $api)
    {
        $this->user = $user;
        $this->referral = $referral;
        $this->setting = $setting;
        $this->api = $api;
    }


    /**
     * show html page for referral settings
     */
    public function showReferralSetting(Request $request)
    {
        $isReferralEnabled = $this->referral->isEnabled();
        $referrerBonusAmount = $this->referral->referrerBonusAmount();
        $referredBonusAmount = $this->referral->referredBonusAmount();
        return view('admin.referral.referral', compact(
            'isReferralEnabled', 'referrerBonusAmount', 'referredBonusAmount'
        ));
    }




    /**
     * save enable or disable status for referral module
     */
    public function saveEnable(Request $request)
    {
        $enable = $request->enable == 'enable' ? 'true' : 'false';
        $this->setting->set('referral_module_enabled', $enable);
        return $this->api->json(true, 'SAVED', 'saved');
    }


    /**
     * save referral bonus amount
     */
    public function saveBonusAmount(Request $request)
    {
        $this->setting->set('referrer_bonus_amount', $request->referrer_bonus_amount);
        $this->setting->set('referred_bonus_amount', $request->referred_bonus_amount);
        return $this->api->json(true, 'SAVED', 'saved');
    }




    /**
     * show user referrals 
     * user referral statistics
     */
    public function showReferralUsers(Request $request)
    {
        
        $users = $this->referral->getReferralUsers($request);

        return view('admin.referral.user_referral_details', compact(
            'users'
        ));

    }




}
