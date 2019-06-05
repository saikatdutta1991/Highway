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
     * page for edit coupon
     */
    public function showEditCoupon(Request $request)
    {
        $coupon = $this->coupon->find($request->coupon_id);
        return view('admin.coupons.add_coupon', compact('coupon'));
    }




    /** 
     * page for add coupon
     */
    public function showAddCoupon()
    { 
        return view('admin.coupons.add_coupon');
    }



    /**
     * update coupon
     */
    public function updateCoupon(Request $request)
    {
        /** validating request other */
        $validator = Validator::make($request->all(), [                
            'code' => 'required|max:128|unique:'.$this->coupon->getTableName().',code,'.$request->coupon_id,
            'name' => 'required|max:256',
            'description' => 'required|max:500',
            'max_uses' => 'required|numeric',
            'max_uses_user' => 'required|numeric',
            'type' => 'required',
            'discount_amount' => 'required|numeric',
            'minimum_purchase' => 'required|numeric',
            'maximum_discount_allowed' => 'required|numeric',
            'discount_type' => 'required',
            'starts_at' => 'required|date_format:d-m-Y h:i A|before_or_equal:expires_at',
            'expires_at' => 'required|date_format:d-m-Y h:i A',
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



        $coupon = $this->coupon->find($request->coupon_id);
        $coupon->code = $request->code;
        $coupon->name = $request->name;
        $coupon->description = $request->description;
        $coupon->max_uses = $request->max_uses;
        $coupon->max_uses_user = $request->max_uses_user;
        $coupon->type = $request->type;
        $coupon->discount_amount = $request->discount_amount;
        $coupon->discount_type = $request->discount_type;
        $coupon->starts_at = $this->utill->strtoutc($request->starts_at, $this->setting->get('default_timezone'), 'd-m-Y h:i A')->toDateTimeString();
        $coupon->expires_at = $this->utill->strtoutc($request->expires_at, $this->setting->get('default_timezone'), 'd-m-Y h:i A')->toDateTimeString();
        $coupon->minimum_purchase = $request->minimum_purchase;
        $coupon->maximum_discount_allowed = $request->maximum_discount_allowed;
        
        if($request->hasFile('banner_picture')) {
            $coupon->banner_picture = CouponModel::savePhoto($request->banner_picture);
        }

        $coupon->save();


        return $this->api->json(true, "COUPON_SAVED", 'Coupon saved', [
            'coupon' => $coupon
        ]);
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
            'minimum_purchase' => 'required|numeric',
            'maximum_discount_allowed' => 'required|numeric',
            'discount_type' => 'required',
            'starts_at' => 'required|date_format:d-m-Y h:i A|before_or_equal:expires_at',
            'expires_at' => 'required|date_format:d-m-Y h:i A',
            'banner_picture' => 'required|image|mimes:png,jpeg,jpg,bmp'
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
        $coupon->starts_at = $this->utill->strtoutc($request->starts_at, $this->setting->get('default_timezone'), 'd-m-Y h:i A')->toDateTimeString();
        $coupon->expires_at = $this->utill->strtoutc($request->expires_at, $this->setting->get('default_timezone'), 'd-m-Y h:i A')->toDateTimeString();
        $coupon->minimum_purchase = $request->minimum_purchase;
        $coupon->maximum_discount_allowed = $request->maximum_discount_allowed;
        $coupon->banner_picture = CouponModel::savePhoto($request->banner_picture);
        $coupon->save();


        return $this->api->json(true, "COUPON_SAVED", 'Coupon saved', [
            'coupon' => $coupon
        ]);

    }





    public function showCoupons()
    {
        $coupons = $this->coupon->withCount('userCoupons')->orderBy('created_at', 'desc')->get();
        return view('admin.coupons.list', compact('coupons'));
    }




    /**
     * show public offers
     */
    public function showOffers()
    {
        $coupons = $this->coupon->withCount('userCoupons')
        ->where('starts_at', '<=', date('Y-m-d H:i:s'))
        ->where('expires_at', '>=', date('Y-m-d H:i:s'))->orderBy('created_at', 'desc')->get();
        return view('coupon_offers', compact('coupons'));   
    }



}
