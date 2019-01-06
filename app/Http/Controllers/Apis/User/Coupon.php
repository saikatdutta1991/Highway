<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupons\Coupon as CouponModel;

class Coupon extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, CouponModel $coupon)
    {
        $this->api = $api;
        $this->coupon = $coupon;
    }


    /**
     * get all couponss
     * coupon_type : city_ride, intracity_trip
     */
    public function getCoupons(Request $request)
    {
        $type = $request->coupon_type;

        $coupons = $this->coupon->withCount('userCoupons')
        ->where('starts_at', '<=', date('Y-m-d H:i:s'))
        ->where('expires_at', '>=', date('Y-m-d H:i:s'))
        ->where(function($query) use($type) {
            $query->where('type', $type)
            ->orWhere('type', 'all');
        })
        ->orderBy('created_at', 'desc')
        ->get();

        return $this->api->json(true, 'COUPONS', 'Coupons fetched', $coupons);
    }




}
