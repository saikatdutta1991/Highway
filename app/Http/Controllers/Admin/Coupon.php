<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupons\Coupon as CouponModel;
use Validator;


class Coupon extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, CouponModel $coupon)
    {
        $this->api = $api;
        $this->coupon = $coupon;
        $this->utill = app('App\Repositories\Utill');
        $this->setting = app('App\Models\Setting');
    }




    /** 
     * page for add coupon
     */
    public function showAddCoupon()
    { 
        return view('admin.coupons.add_coupon');
    }



    /**
     * add coupon api
     */
    public function addCoupon(Request $request)
    {
        /** validating request other */
        $validator = Validator::make($request->all(), [                
            'code' => 'required|max:128|unique:'.$this->coupon->getTableName().',code',
            'name' => 'required|max:256',
            'description' => 'required|max:500',
            'max_uses' => 'required|numeric',
            'max_uses_user' => 'required|numeric',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'discount_type' => 'required',
            'starts_at' => 'required|date_format:Y-m-d H:i|before_or_equal:expires_at',
            'expires_at' => 'required|date_format:Y-m-d H:i'
        ]);

        if($validator->fails()) {
            
            $errors = [];
            foreach($validator->errors()->getMessages() as $fieldName => $msgArr) {
                $errors[$fieldName] = $msgArr[0];
            }
            return $this->api->json(false, 'VALIDATION_ERROR', 'Fill all the fields', [
                'errors' => $errors
            ]);
        }
        
        /**end validating request other */



        $coupon = new $this->coupon;
        $coupon->code = $request->code;
        $coupon->name = $request->name;
        $coupon->description = $request->description;
        $coupon->max_uses = $request->max_uses;
        $coupon->max_uses_user = $request->max_uses_user;
        $coupon->type = $request->type;
        $coupon->discount_amount = $request->discount_amount;
        $coupon->discount_type = $request->discount_type;
        $coupon->starts_at = $this->utill->timestampStringToUTC("{$request->starts_at}:00", $this->setting->get('default_timezone'))->toDateTimeString();
        $coupon->expires_at = $this->utill->timestampStringToUTC("{$request->expires_at}:00", $this->setting->get('default_timezone'))->toDateTimeString();
        $coupon->save();


        return $this->api->json(true, "COUPON_SAVED", 'Coupon saved', [
            'coupon' => $coupon
        ]);

    }





    public function showCoupons()
    {
        $coupons = $this->coupon->withCount('userCoupons')->get();
        return view('admin.coupons.list', compact('coupons'));
    }


}
