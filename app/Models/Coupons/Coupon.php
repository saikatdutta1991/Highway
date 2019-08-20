<?php

namespace App\Models\Coupons;

use Illuminate\Database\Eloquent\Model;
use App\Models\Coupons\UserCoupon;

class Coupon extends Model
{

    const ALL = 'all';
    const CITY_RIDE = 'city_ride';
    const INTRACITY_TRIP = 'intracity_trip';
    const DRIVER_BOOKING = "driver_booking";
    const FLAT = 'flat';
    const PERCENT = 'percentage';

    protected $table = 'coupon_codes';

    protected $hidden = ['banner_picture'];

    protected $appends = ['banner_picture_url'];

    public function getTableName()
    {
        return $this->table;
    }

    public static function tablename()
    {
        return 'coupon_codes';
    }


    /** 
     * save and return photo path
     */
    public static function savePhoto($file)
    {
        $fileName = self::generatePhotoName('coupon', $file->extension());
        $path = self::generatePhotoPath();
        $file->storeAs($path, $fileName);

        return $path.'/'.$fileName;
    }

    /**
     * generate and return path for saving photo
     */
    public static function generatePhotoPath()
    {
        return 'coupons/banners';
    }

    /**
     * get photo 1 url
     */
    public function getBannerPictureUrlAttribute()
    {
        return $this->banner_picture ? url($this->banner_picture) : '';
    }


    /**
     * generate photo name
     */
    public static function generatePhotoName($prefix, $ext)
    {
        $ext = '.'.str_replace('.', '', $ext);
        return $prefix.'_'.md5(uniqid(mt_rand(), true)).'_'.time().$ext;
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

            case 'driver_booking':
                return 'Driver Booking';
                break;
            
        }
    }



    /**
     * get coupon types array list
     */
    protected static function getCouponTypes($couponType) 
    {
        switch ($couponType) {
            case 1:
                return [Coupon::ALL, Coupon::CITY_RIDE];
                break;
            
            case 2:
                return [Coupon::ALL, Coupon::INTRACITY_TRIP];
                break;

            case 3:
                return [Coupon::ALL, Coupon::DRIVER_BOOKING];
                break;

            default:
                return [];
                break;
        }
    }




    /**
     * validate coupon code
     * if valid then returns true
     * else returns error code and message
     */
    public static function isValid($couponCode, $userId, &$coupon, $couponType = 1)
    {
        /** fetching coupon by coupon code */
        $coupon = self::where('code', $couponCode)
        ->whereIn('type', self::getCouponTypes($couponType))
        ->where('starts_at', '<=', date('Y-m-d H:i:s'))
        ->where('expires_at', '>=', date('Y-m-d H:i:s'))
        ->withCount('couponUses')
        ->first();

        if(!$coupon) {
            return ['errcode' => 'INVALID_COUPON', 'errmessage' => 'You have entered invalid coupon code.'];
        }

        /** counting coupon uses by user */
        $usesUser = UserCoupon::where('user_id', $userId)->where('coupon_id', $coupon->id)->count();
        
        if( ($coupon->max_uses > 0 && $coupon->coupon_uses_count >= $coupon->max_uses) || 
            ($coupon->max_uses_user > 0 && $usesUser >= $coupon->max_uses_user)) 
        {
            return ['errcode' => 'MAX_LIMIT', 'errmessage' => 'You have already used this coupon code or limit exceeded.'];
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
               //&& $total >= $this->discount_amount
            if($total > $this->minimum_purchase) { //total must be more than minimum purchase
                
                //if total is more than discount amount
                if($total >= $this->discount_amount) {
                    $total -= $this->discount_amount;
                    $discountAmt = $this->discount_amount;
                } else { //if total amount is less than disocunt amount
                    $discountAmt = $total;
                    $total = 0;
                }
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
