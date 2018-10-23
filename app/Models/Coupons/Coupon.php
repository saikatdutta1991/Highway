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




}