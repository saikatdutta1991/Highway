<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use App\Repositories\PushNotification;
use Illuminate\Http\Request;
use App\Models\Setting;
use Validator;
use App\Models\User;

class UserProfile extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Setting $setting, Api $api, User $user)
    {
        $this->setting = $setting;
        $this->api = $api;
        $this->user = $user;
    }



    /**
     * update firebase push notification token
     */
    public function updatePushToken(Request $request)
    {

        /**
         * validate device type and token should not be null
         */
        if(!in_array($request->device_type, PushNotification::deviceTypes()) && $request->device_token == '') {
            return $this->api->json(false, 'INVALID_PARAMS', 'Parameters invalid or missing');
        }


        $user = $request->auth_user;

        //save device token
        $user->addOrUpdateDeviceToken($request->device_type, $request->device_token);

        return $this->api->json(true, 'PUSH_TOKEN_UPDATED', 'Push token updated');
    }






    /**
     * returns user profile
     */
    public function getUserProfile(Request $request)
    {
        $user = $request->auth_user;

        //dont call save or update on user object
        $user->is_old_password_required = $user->password == '' ? false : true;

        return $this->api->json(true, 'PROFILE', 'Profile fetched', [
            'user' => $user,
            'currency_code' => $this->setting->get('currency_code'),
            'currency_symbol' => $this->setting->get('currency_symbol'),
        ]);

    }







    /** 
     * update user details
     */
    public function updateUserProfile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'fname' => 'sometimes|required|max:128',
            'lname' => 'sometimes|required|max:128',
            'email' => 'sometimes|required|email|max:128',
            'new_password' => 'sometimes|required|min:6|max:100',
            'old_password' => 'sometimes|required|min:6|max:100',
            'country_code' => 'sometimes|required|regex:/^[+].+$/', 
            'mobile_number' => 'sometimes|required|numeric',
            'gender' => 'sometimes|required|in:male,female,other'
        ]);

        if($validator->fails()) {

            $e = $validator->errors();
            $msg = [];
            ($e->has('fname')) ? $msg['fname'] = $e->get('fname')[0] : '';
            ($e->has('lname')) ? $msg['lname'] = $e->get('lname')[0] : '';
            ($e->has('email')) ? $msg['email'] = $e->get('email')[0] : '';
            ($e->has('old_password')) ? $msg['old_password'] = $e->get('old_password')[0] : '';
            ($e->has('new_password')) ? $msg['new_password'] = $e->get('new_password')[0] : '';
            ($e->has('country_code')) ? $msg['country_code'] = $e->get('country_code')[0] : '';
            ($e->has('mobile_number')) ? $msg['mobile_number'] = $e->get('mobile_number')[0] : '';
            ($e->has('gender')) ? $msg['gender'] = $e->get('gender')[0] : '';

            return $this->api->json(false, 'VALIDATION_ERROR', 'Enter all the mandatory fields', $msg);

        }

        $user = $request->auth_user;


        if($request->has('fname')) {
            $user->fname = ucfirst(trim($request->fname));
        }

        if($request->has('lname')) {
            $user->lname = ucfirst($request->lname);
        }

        if($request->has('gender')) {
            $user->gender = $request->gender;
        }

        if($request->has('timezone')) {
            $user->saveTimezone($request->timezone, false);
        }


        // check email in request
        if($request->has('email')) {

            //if email exists check already exists in other user records
            if($this->user->where('email', $request->email)->where('id', '<>', $user->id)->exists()) {
                return $this->api->json(false, 'EMAIL_EXISTS', 'Email id registered with other user. Try another email id');
            } else {
                $user->email = $request->email;
                
                //email changed and unverify 
                $user->is_email_verified = 0;
            }

        }
        


        //check new_password in request
        if($request->has('new_password')) {

            //check old_password required
            $isOldPasswordRequired = $user->password == '' ? false : true;
            if($isOldPasswordRequired && !password_verify($request->old_password, $user->password)) {
                return $this->api->json(false, 'OLD_PASSWORD_UNMATCHED', 'Old password not matched');    
            } 
            
            \Log::info('USER UPDATE PASSWORD UPDATED');
            $user->password = Hash::make($request->new_password);
          
        }



        //check both country code and mobile number
        if($request->has('country_code') && $request->has('mobile_number')) {

            $countryCode = $request->country_code;
            $mobileNumber = $request->mobile_number;
            $exists = $this->user->where(function($query) use($countryCode, $mobileNumber){
                $query->where('country_code', $countryCode)->where('mobile_number', $mobileNumber);
            })->where('id', '<>', $user->id)->exists();


            //mobile number exists for another user
            if($exists) {
                return $this->api->json(false, 'MOBILE_EXISTS', 'Mobile number registered with other user. Try another mobile number');
            } else {
                $user->country_code = $countryCode;
                $user->mobile_number = $mobileNumber;
                $user->full_mobile_number = $countryCode.$mobileNumber;
                
                //mobile changed and unverify 
                $user->is_mobile_number_verified = 0;
            }

        }


        $user->save();
        
        //dont call save or update on user object
        $user->is_old_password_required = $user->password == '' ? false : true;

        return $this->api->json(true, 'PROFILE_UPDATED', 'Profile updated successfully.', [
            'user' => $user,
            'currency_code' => $this->setting->get('currency_code'),
            'currency_symbol' => $this->setting->get('currency_symbol'),
        ]);

    }





}
