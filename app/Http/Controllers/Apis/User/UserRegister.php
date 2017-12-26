<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use App\Repositories\Email;
use Hash;
use App\Repositories\Otp;
use Illuminate\Http\Request;
use App\Models\Setting;
use Validator;
use App\Models\User;

class UserRegister extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Setting $setting, Api $api, User $user, Otp $otp, Email $email)
    {
        $this->setting = $setting;
        $this->api = $api;
        $this->user = $user;
        $this->otp = $otp;
        $this->email = $email;
    }



    public function doRegister(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'fname' => 'required|max:128',
            'lname' => 'required|max:128',
            'email' => 'required|email|max:128',
            'password' => 'required|min:6|max:100',
            'country_code' => 'required|regex:/^[+].+$/', 
            'mobile_number' => 'required|numeric',
        ]);


        if($validator->fails()) {

            $e = $validator->errors();
            $msg = [];
            ($e->has('fname')) ? $msg['fname'] = $e->get('fname')[0] : '';
            ($e->has('lname')) ? $msg['lname'] = $e->get('lname')[0] : '';
            ($e->has('email')) ? $msg['email'] = $e->get('email')[0] : '';
            ($e->has('password')) ? $msg['password'] = $e->get('password')[0] : '';
            ($e->has('country_code')) ? $msg['country_code'] = $e->get('country_code')[0] : '';
            ($e->has('mobile_number')) ? $msg['mobile_number'] = $e->get('mobile_number')[0] : '';

            return $this->api->json(false, 'VALIDATION_ERROR', 'Enter all the mandatory fields', $msg);

        }



        //check mobile number already exists
        $full_mobile_number = $request->country_code.$request->mobile_number;
        if($this->user->where('full_mobile_number', $full_mobile_number)->exists()) {
            return $this->api->json(false, 'MOBILE_NUMBER_DUPLICATE', 'Mobile number is already registered. Try another mobile number.');
        }


        //check email number already exists
        if($this->user->where('email', $request->email)->exists()) {
            return $this->api->json(false, 'EMAIL_DUPLICATE', 'Email is already registered. Try another email address.');
        }


        $user = new $this->user;
        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->email = $request->email;
        $user->is_email_verified = 0;
        $user->password = Hash::make($request->password);
        $user->country_code = $request->country_code;
        $user->mobile_number = $request->mobile_number;
        $user->is_mobile_number_verified = 0;
        $user->full_mobile_number = $user->fullMobileNumber();
        $user->last_access_time = date('Y-m-d H:i:s');
        $user->last_accessed_ip = $request->ip();
        
        DB::beginTransaction();

        try {

            $user->save();

            //save device token
            $user->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($user->id, 'user')->access_token;

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            \Log::info('USER_REGISTRATION');
            \Log::info($e->getMessage());

            return $this->api->json(false, 'REGISTER_FAILED', 'Unknown server error. Contact to service provider.');
            
        }

        //send welcome email through queue
        $this->email->sendNewUserWelcomeEmail($user);
       
        return $this->api->json(true, 'REGISTER_SUCCESS', 'You have registered successfully.', [
            'accesss_token' => $accessToken,
            'currency_code' => $this->setting->get('currency_code'),
            'currency_symbol' => $this->setting->get('currency_symbol'),
            'user' => $user
        ]);

    }






    /**
     * user login 
     */
    public function doLogin(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|max:100',
            'country_code' => 'required|regex:/^[+].+$/', 
            'mobile_number' => 'required|numeric',
        ]);


        if($validator->fails()) {

            $e = $validator->errors();
            $msg = [];
            ($e->has('password')) ? $msg['password'] = $e->get('password')[0] : '';
            ($e->has('country_code')) ? $msg['country_code'] = $e->get('country_code')[0] : '';
            ($e->has('mobile_number')) ? $msg['mobile_number'] = $e->get('mobile_number')[0] : '';

            return $this->api->json(false, 'VALIDATION_ERROR', 'Enter all the mandatory fields', $msg);

        }


        //check mobile number is registered 
        $user = $this->user->where('country_code', $request->country_code)
        ->where('mobile_number', $request->mobile_number)->first();

        if(!$user) {
            return $this->api->json(false, 'MOBILE_NUMBER_NOT_REGISTERED', 'Mobile number is not registered with us.');
        }


        //check password match
        if(!password_verify($request->password, $user->password)){
            return $this->api->json(false, 'INCORRECT_PASSWORD', 'Incorrect password');
        }


        if($user->status != 'ACTIVATED') {
            return $this->api->json(false, 'NOT_ACTIVATED', 'User not activated', [
                'reason' => $user->reasonNotActivated()
            ]);
        }

        //save iser timezone
        $user->saveTimezone($request->timezone, true);

        //save device token
        $user->addOrUpdateDeviceToken($request->device_type, $request->device_token);

        //create and save accesstoken
        $accessToken = $this->api->saveAccessToken($user->id, 'user')->access_token;

        return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
            'accesss_token' => $accessToken,
            'currency_code' => $this->setting->get('currency_code'),
            'currency_symbol' => $this->setting->get('currency_symbol'),
            'user' => $user
        ]);

    }









    /**
     * send otp to user
     */
    public function sendOtp(Request $request)
    {
        
        $user = $request->auth_user;

        if($user->full_mobile_number == '') {
            return $this->api->json(false, 'ADD_MOBILE_NUMBER', 'Add mobile number first. Then try sending otp');
        }


        //sending otp
        $success = $this->otp->sendOTP(
            $user->country_code, 
            $user->mobile_number, 
            'Your one time password is : {{otp_code}}',
            $user->id, 
            $error
        );


        if($success) {
            return $this->api->json(true, 'OTP_SENT', 'Otp has been sent to your registered mobile number.');
        } else {
            return $this->api->json(false, 'OTP_SEND_FAILED', 'Failed to send otp. Try again.');
        }


    }





    /**
     * verify user otp and make mobile number verified
     */
    public function verifydOtp(Request $request)
    {
        $user = $request->auth_user;

        if($this->otp->verifyOTP($user->country_code, $user->mobile_number, $request->otp_code)){

            //verify mobile number
            $user->is_mobile_number_verified = 1;
            $user->save();

            return $this->api->json(true, 'OTP_VERIFIED', 'Mobile verified successfully.');
        }

        return $this->api->json(false, 'OTP_VERIFY_FAILED', 'Invalid otp code entered or expired.');

    }






    /**
     * get all vehicle types
     */
    public function getVehicleTypes()
    {
        return $this->api->json(true, 'VEHICLE_TYPES', 'Vehicle types', [
            'vtypes' => app('App\Models\VehicleType')->allTypes()
        ]);
    }




}
