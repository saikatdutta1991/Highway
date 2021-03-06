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
        $this->utillRepo = app('UtillRepo');
    }



    /**
     * calculate total after deducting referreal bonus
     */
    public function deductBounus($userId, $total)
    {
        if(!$this->isEnabled()) {
            return false;
        }

        $referral = $this->createReferralCodeIfNotExists('user', $userId);
        
        $bonusDiscount = 0;
        if($total >= $referral->bonus_amount) {
            $total -= $referral->bonus_amount;
            $bonusDiscount = $referral->bonus_amount;
        } else {
            $bonusDiscount = $total;
            $total = 0;
        }
        
        return ['total' => $this->utillRepo->formatAmountDecimalTwo($total), 'bonusDiscount' => $this->utillRepo->formatAmountDecimalTwo($bonusDiscount)];

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
        return $this->utillRepo->randomChars(6);
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


        //send referrer user email 
        $toEmail = $referralHistory->referrerUser->email;
        $name = $referralHistory->referrerUser->fname;
        $subject = 'Earned from referral';
        $messageBody = "Your friend {$referralHistory->referredUser->fname} has registered with your referral code just now. 
        <br> You have earned {$referralHistory->referrer_bonus_amount}. Enjoy !!";
        app('App\Repositories\Email')->sendCommonEmail($toEmail, $name, $subject, $messageBody);


        //send referred user email
        $toEmail = $referralHistory->referredUser->email;
        $name = $referralHistory->referredUser->fname;
        $subject = 'Earned from referral';
        $messageBody = "You have just earned {$referralHistory->referred_bonus_amount}. Enjoy !!";
        app('App\Repositories\Email')->sendCommonEmail($toEmail, $name, $subject, $messageBody);


        return $referralHistory;


    }



    /**
     * get referral users list for admin panel
     */
    public function getReferralUsers($request, $path = 'referral/users')
    {
        $uModel = app('App\Models\User');
        $ut = $uModel->getTableName();
        $rht = $this->referralHistory->getTableName();
        
        $users = $uModel->join($rht, function ($join) use($rht, $ut) {
            $join->on($ut.'.id', '=', $rht.'.referrer_id')
            ->where($rht.'.referrer_type', 'user');
        })
        ->select([$ut.'.*', DB::raw('COUNT(referrer_id) as referred_count')])
        ->groupBy($ut.'.id')
        ->orderBy('referred_count');

        if($request->has('specific_user_id')) {
            $users = $users->where($ut.'.id', $request->specific_user_id);
        }

        $users = $users->paginate(100)->setPath($path);

        
        
        //modify items: add referred users
        $users->getCollection()->transform(function ($user) {
            
            $referralHistories = $this->referralHistory
            ->where('referrer_type', 'user')
            ->where('referrer_id', $user->id)
            ->with('referredUser')
            ->get();

            $user['referral_histories'] = $referralHistories;
            $user['referral_code'] = $this->referralCode->where('e_type', 'user')->where('e_id', $user->id)->first();
            return $user;
        });

      
        return $users;
        

    }



}
