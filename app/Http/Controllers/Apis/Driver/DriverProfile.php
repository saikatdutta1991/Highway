<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use App\Repositories\PushNotification;
use Illuminate\Http\Request;
use Validator;
use App\Models\Driver;
use App\Models\DriverBank;
use App\Models\Setting;
use App\Models\VehicleType;

class DriverProfile extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Setting $setting, Api $api, Driver $driver, VehicleType $vehicleType)
    {
        $this->setting = $setting;
        $this->api = $api;
        $this->driver = $driver;
        $this->vehicleType = $vehicleType;
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


        $driver = $request->auth_driver;

        //save device token
        $driver->addOrUpdateDeviceToken($request->device_type, $request->device_token);

        return $this->api->json(true, 'PUSH_TOKEN_UPDATED', 'Push token updated');
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
        $driver->extra_photos_urls = $driver->getExtraPhotosUrl();
        $driver->bank;


        return $this->api->json(true, 'PROFILE', 'Profile fetched', [
            'driver' => $driver,
            'currency_code' => $this->setting->get('currency_code'),
            'currency_symbol' => $this->setting->get('currency_symbol'),
        ]);

    }







    /** 
     * update driver bank details
     * if bank details does not exists then create new recorod
     */
    public function updateBank(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bank_name' => 'required|max:128',
            'bank_account_holder_name' => 'required|max:256',
            'bank_ifsc_code' => 'required|max:50',
            'bank_account_number' => 'required|max:50',
            'bank_extra_info' => 'min:5|max:256'
        ]);

        if($validator->fails()) {

            $e = $validator->errors();
            $msg = [];
            ($e->has('bank_name')) ? $msg['bank_name'] = $e->get('bank_name')[0] : '';
            ($e->has('bank_account_holder_name')) ? $msg['bank_account_holder_name'] = $e->get('bank_account_holder_name')[0] : '';
            ($e->has('bank_ifsc_code')) ? $msg['bank_ifsc_code'] = $e->get('bank_ifsc_code')[0] : '';
            ($e->has('bank_account_number')) ? $msg['bank_account_number'] = $e->get('bank_account_number')[0] : '';
            ($e->has('bank_extra_info')) ? $msg['bank_extra_info'] = $e->get('bank_extra_info')[0] : '';

            return $this->api->json(false, 'VALIDATION_ERROR', 'Enter all the mandatory fields', $msg);

        }   

        /** get driver from request */
        $driver = $request->auth_driver;


        /** save driver bank */
        $bank = DriverBank::where('driver_id', $driver->id)->first() ? : new DriverBank;
        $bank->driver_id = $driver->id;
        $bank->bank_name = ucwords($request->bank_name);
        $bank->account_holder_name = ucwords($request->bank_account_holder_name);
        $bank->ifsc_code = ucfirst($request->bank_ifsc_code);
        $bank->account_number = strtoupper($request->bank_account_number);
        $bank->extra_info = $request->bank_extra_info ?: '';
        $bank->save(); 

        return $this->api->json(true, 'BANK_UPDATED', 'Bank updated successfully.', [
            'bank' => $bank
        ]);



    }











    /** 
     * update driver details
     */
    public function updateDriverProfile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'is_available' => 'sometimes|required|in:0,1',
            'fname' => 'sometimes|required|max:128',
            'lname' => 'sometimes|required|max:128',
            'email' => 'sometimes|required|email|max:128',
            'old_password' => 'sometimes|required|min:6|max:100',
            'new_password' => 'sometimes|required|min:6|max:100',
            'country_code' => 'sometimes|required|regex:/^[+].+$/', 
            'mobile_number' => 'sometimes|required|numeric',
            'photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            //'vehicle_type' => 'sometimes|required_if:ready_to_get_hired,0|in:'.implode(',', $this->vehicleType->allCodes()),
            'vehicle_number' => 'sometimes|required_if:ready_to_get_hired,0',
            'vehicle_registration_certificate_photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_contract_permit_photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_insurance_certificate_photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_fitness_certificate_photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_lease_agreement_photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_photo_first' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_photo_second' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_photo_third' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_photo_fourth' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_commercial_driving_license_plate_photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'vehicle_police_verification_certificate_photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'bank_passbook_or_canceled_check_photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'aadhaar_card_photo' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'manual_transmission' => 'sometimes|required|boolean',
            'automatic_transmission' => 'sometimes|required|boolean',
            'ready_to_get_hired' => 'sometimes|required|boolean'
        ]);

        if($validator->fails()) {

            $messages = [];
            foreach($validator->errors()->getMessages() as $attr => $errArray) {
                $messages[$attr] = $errArray[0];
            }

            return $this->api->json(false, 'VALIDATION_ERROR', 'Enter all the mandatory fields', $messages);

        }

        $driver = $request->auth_driver;

        if($request->has('ready_to_get_hired')) {
            $driver->ready_to_get_hired = $request->ready_to_get_hired;
        }

        if($request->has('manual_transmission')) {
            $driver->manual_transmission = $request->manual_transmission;
        }

        if($request->has('automatic_transmission')) {
            $driver->automatic_transmission = $request->automatic_transmission;
        }

        if($request->has('is_available')) {
            $driver->is_available = $request->is_available == 1 ? 1 : 0;
        }

        if($request->has('fname')) {
            $driver->fname = ucfirst(trim($request->fname));
        }

        if($request->has('lname')) {
            $driver->lname = ucfirst(trim($request->lname));
        }

        if($request->has('vehicle_number')) {
            $driver->vehicle_number = strtoupper($request->vehicle_number);
        }

        // if($request->has('vehicle_type')) {
        //     $driver->vehicle_type = $request->vehicle_type;
        // }

        
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



        //save extra images like rc photo, driving license photo etc.
        $driver->saveExtraPhotos(
            $request->vehicle_registration_certificate_photo, 
            $request->vehicle_contract_permit_photo, 
            $request->vehicle_insurance_certificate_photo, 
            $request->vehicle_fitness_certificate_photo, 
            $request->vehicle_lease_agreement_photo, 
            $request->vehicle_photo_first, 
            $request->vehicle_photo_second, 
            $request->vehicle_photo_third, 
            $request->vehicle_photo_fourth, 
            $request->vehicle_commercial_driving_license_plate_photo, 
            $request->vehicle_police_verification_certificate_photo, 
            $request->bank_passbook_or_canceled_check_photo, 
            $request->aadhaar_card_photo
        );


        $driver->save();


        //dont call save or update on driver object
        $driver->is_old_password_required = $driver->password == '' ? false : true;
        $driver->profile_photo_url = $driver->profilePhotoUrl();
        $driver->extra_photos_urls = $driver->getExtraPhotosUrl();

        return $this->api->json(true, 'PROFILE_UPDATED', 'Profile updated successfully.', [
            'driver' => $driver,
            'currency_code' => $this->setting->get('currency_code'),
            'currency_symbol' => $this->setting->get('currency_symbol'),
        ]);

    }





}
