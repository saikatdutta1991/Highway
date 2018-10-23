<?php

namespace App\Models\Coupons;

use Illuminate\Database\Eloquent\Model;

class UserCoupon extends Model
{

    protected $table = 'user_coupon_uses';

    public function getTableName()
    {
        return $this->table;
    }

    public static function tablename()
    {
        return 'user_coupon_uses';
    }


}