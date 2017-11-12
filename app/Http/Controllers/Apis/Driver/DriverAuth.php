<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use App\Models\Driver;
use Hash;
use App\Repositories\Otp;
use Illuminate\Http\Request;
use App\Models\VehicleType;
use Validator;


class DriverAuth extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, Driver $driver, VehicleType $vehicleType, Otp $otp)
    {
        $this->api = $api;
        $this->driver = $driver;
        $this->vehicleType = $vehicleType;
        $this->otp = $otp;
    }



    /**
     * get vehicle types
     */
    public function getVehicleTypes()
    {
        return $this->api->json(true, 'VEHICLE_TYPES', 'Vehicle types', [
            'vtypes' => $this->vehicleType->allTypes()
        ]);
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
            'photo' => 'required|image|mimes:jpg,jpeg,png',
            'vehicle_type' => 'required|in:'.implode(',', $this->vehicleType->allCodes()),
            'vehicle_number' => 'required'
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
            ($e->has('photo')) ? $msg['photo'] = $e->get('photo')[0] : '';
            ($e->has('vehicle_type')) ? $msg['vehicle_type'] = $e->get('vehicle_type')[0] : '';
            ($e->has('vehicle_number')) ? $msg['vehicle_number'] = $e->get('vehicle_number')[0] : '';

            return $this->api->json(false, 'VALIDATION_ERROR', 'Enter all the mandatory fields', $msg);

        }


        //check mobile number already exists
        $full_mobile_number = $request->country_code.$request->mobile_number;
        if($this->driver->where('full_mobile_number', $full_mobile_number)->exists()) {
            return $this->api->json(false, 'MOBILE_NUMBER_DUPLICATE', 'Mobile number is already registered. Try another mobile number.');
        }


        //check email number already exists
        if($this->driver->where('email', $request->email)->exists()) {
            return $this->api->json(false, 'EMAIL_DUPLICATE', 'Email is already registered. Try another email address.');
        }


        $driver = new $this->driver;
        $driver->fname = $request->fname;
        $driver->lname = $request->lname;
        $driver->email = $request->email;
        $driver->is_email_verified = 0;
        $driver->password = Hash::make($request->password);
        $driver->country_code = $request->country_code;
        $driver->mobile_number = $request->mobile_number;
        $driver->is_mobile_number_verified = 0;
        $driver->full_mobile_number = $driver->fullMobileNumber();
        $driver->last_access_time = date('Y-m-d H:i:s');
        $driver->last_accessed_ip = $request->ip();
        $driver->vehicle_type = $request->vehicle_type;
        $driver->vehicle_number = strtoupper($request->vehicle_number);


        DB::beginTransaction();

        try {
            
            $driver->savePhoto($request->photo, 'driver_');
            $driver->save();

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($driver->id, 'driver')->access_token;

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            \Log::info('DRIVER_REGISTRATION');
            \Log::info($e->getMessage());
    
            return $this->api->json(false, 'REGISTER_FAILED', 'Unknown server error. Contact to service provider.');
            
        }


        //adding profile photo url dont save after adding this attribute
        $driver->profile_photo_url = $driver->profilePhotoUrl();

        return $this->api->json(true, 'REGISTER_SUCCESS', 'You have registered successfully.', [
            'accesss_token' => $accessToken,
            'driver' => $driver
        ]);
        

    }






    /**
     * Driver login 
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
        $driver = $this->driver->where('country_code', $request->country_code)
        ->where('mobile_number', $request->mobile_number)->first();

        if(!$driver) {
            return $this->api->json(false, 'MOBILE_NUMBER_NOT_REGISTERED', 'Mobile number is not registered with us.');
        }


        //check password match
        if(!password_verify($request->password, $driver->password)){
            return $this->api->json(false, 'INCORRECT_PASSWORD', 'Incorrect password');
        }


        if($driver->status != 'ACTIVATED') {
            return $this->api->json(false, 'NOT_ACTIVATED', 'You account is not activated', [
                'reason' => $driver->reasonNotActivated()
            ]);
        }


        //create and save accesstoken
        $accessToken = $this->api->saveAccessToken($driver->id, 'driver')->access_token;

        //adding profile photo url dont save after adding this attribute
        $driver->profile_photo_url = $driver->profilePhotoUrl();

        return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
            'accesss_token' => $accessToken,
            'driver' => $driver
        ]);

        

    }



     /**
     * send otp to driver
     */
    public function sendOtp(Request $request)
    {
        
        $driver = $request->auth_driver;

        if($driver->full_mobile_number == '') {
            return $this->api->json(false, 'ADD_MOBILE_NUMBER', 'Add mobile number first. Then try sending otp');
        }


        //sending otp
        $success = $this->otp->sendOTP(
            $driver->country_code, 
            $driver->mobile_number, 
            'Your one time password is : {{otp_code}}',
            $driver->id, 
            $error
        );


        if($success) {
            return $this->api->json(true, 'OTP_SENT', 'Otp has been sent to your registered mobile number.');
        } else {
            return $this->api->json(false, 'OTP_SEND_FAILED', 'Failed to send otp. Try again.');
        }


    }





    /**
     * verify driver otp and make mobile number verified
     */
    public function verifydOtp(Request $request)
    {
        $driver = $request->auth_driver;

        if($this->otp->verifyOTP($driver->country_code, $driver->mobile_number, $request->otp_code)){

            //verify mobile number
            $driver->is_mobile_number_verified = 1;
            $driver->save();

            return $this->api->json(false, 'OTP_VERIFIED', 'Mobile verified successfully.');
        }

        return $this->api->json(false, 'OTP_VERIFY_FAILED', 'Invalid otp code entered or expired.');

    }







}
