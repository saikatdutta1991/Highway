<?php

namespace App\Models\Coupons;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coupons\UserCoupon;

class Coupon extends Model
{

    const ALL = 'all';
    const CITY_RIDE = 'city_ride';
    const INTRACITY_TRIP = 'intracity_trip';
    const FLAT = 'flat';
    const PERCENT = 'percentage';

    protected $table = 'coupon_codes';

    public function getTableName()
    {
        return $this->table;
    }

    public static function tablename()
    {
        return 'coupon_codes';
    }



    /**
     * relation with user coupon
     */
    public function userCoupons()
    {
        return $this->hasMany('App\Models\Coupons\UserCoupon', 'coupon_id');
    }


    /**
     * relation with user coupon uses
     */
    public function couponUses()
    {
        return $this->hasMany('App\Models\Coupons\UserCoupon', 'coupon_id');
    }



    /** 
     * formated starts_at
     */
    public function formatedStartsAt($timezone)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->starts_at)->setTimezone($timezone)->format('d-m-Y h:i A');
    }

    /** 
     * formated expires_at
     */
    public function formatedExpiresAt($timezone)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->expires_at)->setTimezone($timezone)->format('d-m-Y h:i A');
    }



    /**
     * formated coupon type
     */
    public function formatedCouponType()
    {
        switch ($this->type) {
            case 'all':
                return 'All';
                break;
            
            case 'city_ride':
                return 'City';
                break;

            case 'intracity_trip':
                return 'Intracity Trip';
                break;
            
        }
    }



    /**
     * validate coupon code
     * if valid then returns true
     * else returns error code and message
     */
    public static function isValid($couponCode, $userId, &$coupon)
    {
        /** fetching coupon by coupon code */
        $coupon = self::where('code', $couponCode)
        ->whereIn('type', [Coupon::ALL, Coupon::CITY_RIDE])
        ->where('starts_at', '<=', date('Y-m-d H:i:s'))
        ->where('expires_at', '>=', date('Y-m-d H:i:s'))
        ->withCount('couponUses')
        ->first();

        if(!$coupon) {
            return ['errcode' => 'INVALID_COUPON', 'errmessage' => 'Coupon code is invalid'];
        }

        /** counting coupon uses by user */
        $usesUser = UserCoupon::where('user_id', $userId)->where('coupon_id', $coupon->id)->count();
        
        if( ($coupon->max_uses > 0 && $coupon->coupon_uses_count >= $coupon->max_uses) || 
            ($coupon->max_uses_user > 0 && $usesUser >= $coupon->max_uses_user)) 
        {
            return ['errcode' => 'MAX_LIMIT', 'errmessage' => 'Coupon code uses limit exceeded'];
        }

        return true;

    }



    /**
     * calculate coupon discount
     */
    public function calculateDiscount($total)
    {       
        $discountAmt = 0;
        if($this->discount_type == Coupon::FLAT) {

            if($total > $this->minimum_purchase && $total >= $this->discount_amount) {
                $total -= $this->discount_amount;
                $discountAmt = $this->discount_amount;
            }
    

        } else if($this->discount_type == Coupon::PERCENT){
            
            $discountAmt = $total * ($this->discount_amount / 100);
            $discountAmt = $discountAmt > $this->maximum_discount_allowed ? $this->maximum_discount_allowed : $discountAmt;

            if($total >= $discountAmt) {
                $total -= $discountAmt;
            } else {
                $discountAmt = $total;
                $total = 0;
            }


        }

        $utillRepo = app('UtillRepo');
        return ['total' => $total, 'coupon_discount' => $utillRepo->formatAmountDecimalTwo($discountAmt)];

    }




}