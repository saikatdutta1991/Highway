<?php

namespace App\Models\Coupons;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coupons\Coupon;

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


    /** 
     * @param $coupon --> can be coupon code or id
     */
    public static function markUsed( $userid, $coupon )
    {
        $cc = Coupon::where(function( $query ) use( $coupon ) {
            $query->where( "id", $coupon )
                ->orWhere( "code", $coupon );
        })->select( [ "id", "code" ] )->first();


        if( $cc ) {

            $userCoupon = new UserCoupon;
            $userCoupon->user_id = $userid;
            $userCoupon->coupon_id = $cc->id;
            $userCoupon->save();

        }

    }


}