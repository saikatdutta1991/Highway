<?php

namespace App\Models\Coupons;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{

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



}