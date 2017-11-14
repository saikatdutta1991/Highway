<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use Illuminate\Http\Request;
use Validator;
use App\Models\Driver;
use App\Models\VehicleType;

class DriverProfile extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, Driver $driver, VehicleType $vehicleType)
    {
        $this->api = $api;
        $this->driver = $driver;
        $this->vehicleType = $vehicleType;
    }



    /**
     * returns driver profile
     */
    public function getDriverProfile(Request $request)
    {
        $driver = $request->auth_driver;

        //dont call save or update on driver object
        $driver->is_old_password_required = $driver->password == '' ? false : true;
        $driver->profile_photo_url = $driver->profilePhotoUrl();

        return $this->api->json(true, 'PROFILE', 'Profile fetched', [
            'driver' => $driver
        ]);

    }







    /** 
     * update driver details
     */
    public function updateDriverProfile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'fname' => 'sometimes|required|max:128',
            'lname' => 'sometimes|required|max:128',
            'email' => 'sometimes|required|email|max:128',
            'old_password' => 'sometimes|required|min:6|max:100',
            'new_password' => 'sometimes|required|min:6|max:100',
            'country_code' => 'sometimes|required|regex:/^[+].+$/', 
            'mobile_number' => 'sometimes|required|numeric',
            'photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_type' => 'sometimes|required|in:'.implode(',', $this->vehicleType->allCodes()),
            'vehicle_number' => 'sometimes|required'
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
            ($e->has('photo')) ? $msg['photo'] = $e->get('photo')[0] : '';
            ($e->has('vehicle_type')) ? $msg['vehicle_type'] = $e->get('vehicle_type')[0] : '';
            ($e->has('vehicle_number')) ? $msg['vehicle_number'] = $e->get('vehicle_number')[0] : '';

            return $this->api->json(false, 'VALIDATION_ERROR', 'Enter all the mandatory fields', $msg);

        }

        $driver = $request->auth_driver;


        if($request->has('fname')) {
            $driver->fname = trim($request->fname);
        }

        if($request->has('lname')) {
            $driver->lname = trim($request->lname);
        }

        if($request->has('vehicle_number')) {
            $driver->vehicle_number = $request->vehicle_number;
        }

        if($request->has('vehicle_type')) {
            $driver->vehicle_type = $request->vehicle_type;
        }

        
        // check email in request
        if($request->has('email')) {

            //if email exists check already exists in other driver records
            if($this->driver->where('email', $request->email)->where('id', '<>', $driver->id)->exists()) {
                return $this->api->json(false, 'EMAIL_EXISTS', 'Email id registered with other driver. Try another email id');
            } else {
                $driver->email = $request->email;
                
                //email changed and unverify 
                $driver->is_email_verified = 0;
            }

        }
        
        

        //check new_password in request
        if($request->has('new_password')) {

            //check old_password required
            $isOldPasswordRequired = $driver->password == '' ? false : true;
            if($isOldPasswordRequired && !password_verify($request->old_password, $driver->password)) {
                return $this->api->json(false, 'OLD_PASSWORD_UNMATCHED', 'Old password not matched');    
            } 
            
            \Log::info('DRIVER UPDATE PASSWORD UPDATED');
            $driver->password = Hash::make($request->new_password);
          
        }



        //check both country code and mobile number
        if($request->has('country_code') && $request->has('mobile_number')) {

            $countryCode = $request->country_code;
            $mobileNumber = $request->mobile_number;
            $exists = $this->driver->where(function($query) use($countryCode, $mobileNumber){
                $query->where('country_code', $countryCode)->where('mobile_number', $mobileNumber);
            })->where('id', '<>', $driver->id)->exists();


            //mobile number exists for another driver
            if($exists) {
                return $this->api->json(false, 'MOBILE_EXISTS', 'Mobile number registered with other driver. Try another mobile number');
            } else {
                $driver->country_code = $countryCode;
                $driver->mobile_number = $mobileNumber;
                $driver->full_mobile_number = $countryCode.$mobileNumber;
                
                //mobile changed and unverify 
                $driver->is_mobile_number_verified = 0;
            }

        }


        //save photo
        if($request->has('photo')) {
            $driver->savePhoto($request->photo, 'driver_');
        }



        $driver->save();
        
        //dont call save or update on driver object
        $driver->is_old_password_required = $driver->password == '' ? false : true;
        $driver->profile_photo_url = $driver->profilePhotoUrl();

        return $this->api->json(true, 'PROFILE_UPDATED', 'Profile updated successfully.', [
            'driver' => $driver
        ]);

    }





}
