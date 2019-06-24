<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use App\Models\Driver;
use App\Models\DriverBank;
use App\Repositories\Email;
use Hash;
use App\Repositories\Otp;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\VehicleType;
use Validator;


class DriverAuth extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Setting $setting, Api $api, Driver $driver, VehicleType $vehicleType, Otp $otp, Email $email)
    {
        $this->setting = $setting;
        $this->api = $api;
        $this->driver = $driver;
        $this->vehicleType = $vehicleType;
        $this->otp = $otp;
        $this->email = $email;
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
            'vehicle_number' => 'required',
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
            'bank_name' => 'required|max:128',
            'bank_account_holder_name' => 'required|max:256',
            'bank_ifsc_code' => 'required|max:50',
            'bank_account_number' => 'required|max:50',
            'bank_extra_info' => 'min:5|max:256'

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
            ($e->has('vehicle_registration_certificate_photo')) ? $msg['vehicle_registration_certificate_photo'] = $e->get('vehicle_registration_certificate_photo')[0] : '';
            ($e->has('vehicle_contract_permit_photo')) ? $msg['vehicle_contract_permit_photo'] = $e->get('vehicle_contract_permit_photo')[0] : '';
            ($e->has('vehicle_insurance_certificate_photo')) ? $msg['vehicle_insurance_certificate_photo'] = $e->get('vehicle_insurance_certificate_photo')[0] : '';
            ($e->has('vehicle_fitness_certificate_photo')) ? $msg['vehicle_fitness_certificate_photo'] = $e->get('vehicle_fitness_certificate_photo')[0] : '';
            ($e->has('vehicle_lease_agreement_photo')) ? $msg['vehicle_lease_agreement_photo'] = $e->get('vehicle_lease_agreement_photo')[0] : '';
            ($e->has('vehicle_photo_first')) ? $msg['vehicle_photo_first'] = $e->get('vehicle_photo_first')[0] : '';
            ($e->has('vehicle_photo_second')) ? $msg['vehicle_photo_second'] = $e->get('vehicle_photo_second')[0] : '';
            ($e->has('vehicle_photo_third')) ? $msg['vehicle_photo_third'] = $e->get('vehicle_photo_third')[0] : '';
            ($e->has('vehicle_photo_fourth')) ? $msg['vehicle_photo_fourth'] = $e->get('vehicle_photo_fourth')[0] : '';
            ($e->has('vehicle_commercial_driving_license_plate_photo')) ? $msg['vehicle_commercial_driving_license_plate_photo'] = $e->get('vehicle_commercial_driving_license_plate_photo')[0] : '';
            ($e->has('vehicle_police_verification_certificate_photo')) ? $msg['vehicle_police_verification_certificate_photo'] = $e->get('vehicle_police_verification_certificate_photo')[0] : '';
            ($e->has('bank_passbook_or_canceled_check_photo')) ? $msg['bank_passbook_or_canceled_check_photo'] = $e->get('bank_passbook_or_canceled_check_photo')[0] : '';
            ($e->has('aadhaar_card_photo')) ? $msg['aadhaar_card_photo'] = $e->get('aadhaar_card_photo')[0] : '';
            ($e->has('bank_name')) ? $msg['bank_name'] = $e->get('bank_name')[0] : '';
            ($e->has('bank_account_holder_name')) ? $msg['bank_account_holder_name'] = $e->get('bank_account_holder_name')[0] : '';
            ($e->has('bank_ifsc_code')) ? $msg['bank_ifsc_code'] = $e->get('bank_ifsc_code')[0] : '';
            ($e->has('bank_account_number')) ? $msg['bank_account_number'] = $e->get('bank_account_number')[0] : '';
            ($e->has('bank_extra_info')) ? $msg['bank_extra_info'] = $e->get('bank_extra_info')[0] : '';

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
        $driver->fname = ucfirst($request->fname);
        $driver->lname = ucfirst($request->lname);
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

        //by default is_approved will be 0
        $driver->is_approved = 0;

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

        //save driver timezone
        $driver->saveTimezone($request->timezone, false);


        //add driver bank details
        $bank = new DriverBank;
        $bank->bank_name = ucwords($request->bank_name);
        $bank->account_holder_name = ucwords($request->bank_account_holder_name);
        $bank->ifsc_code = ucfirst($request->bank_ifsc_code);
        $bank->account_number = strtoupper($request->bank_account_number);
        $bank->extra_info = $request->bank_extra_info ?: '';


        DB::beginTransaction();

        try {
            
            $driver->savePhoto($request->photo, 'driver_');
            $driver->save();

            //save device token
            $driver->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($driver->id, 'driver')->access_token;

            $bank->driver_id = $driver->id;
            $bank->save(); //insert bank details

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            \Log::info('DRIVER_REGISTRATION');
            \Log::info($e->getMessage());
    
            return $this->api->json(false, 'REGISTER_FAILED', 'Unknown server error. Contact to service provider.');
            
        }


        //adding profile photo url dont save after adding this attribute
        $driver->profile_photo_url = $driver->profilePhotoUrl();
        $driver->extra_photos_urls = $driver->getExtraPhotosUrl();
        $driver->bank;


        //send new driver registration mail
        $this->email->sendNewDriverWelcomeEmail($driver);

        return $this->api->json(true, 'REGISTER_SUCCESS', 'You have registered successfully.', [
            'accesss_token' => $accessToken,
            'currency_code' => $this->setting->get('currency_code'),
            'currency_symbol' => $this->setting->get('currency_symbol'),
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


        //save device token
        $driver->addOrUpdateDeviceToken($request->device_type, $request->device_token);

        //create and save accesstoken
        $accessToken = $this->api->saveAccessToken($driver->id, 'driver')->access_token;


        //save driver timezone
        $driver->saveTimezone($request->timezone, true);
        


        //adding profile photo url dont save after adding this attribute
        $driver->profile_photo_url = $driver->profilePhotoUrl();
        $driver->extra_photos_urls = $driver->getExtraPhotosUrl();
        $driver->bank;

        return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
            'accesss_token' => $accessToken,
            'currency_code' => $this->setting->get('currency_code'),
            'currency_symbol' => $this->setting->get('currency_symbol'),
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
            'android', //this tells the api that message for android device
            'driver', //this says the api that message for driver app
            $driver->country_code, 
            $driver->mobile_number,
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

            return $this->api->json(true, 'OTP_VERIFIED', 'Mobile verified successfully.');
        }

        return $this->api->json(false, 'OTP_VERIFY_FAILED', 'Invalid otp code entered or expired.');

    }










     /**
     * send password reset code to mobile and email
     */
    public function sendPasswordReset(Request $request)
    {

        //creating full mobile number
        $full_mobile_number = $request->country_code.$request->mobile_number;
        $driver = $this->driver->where('full_mobile_number', $full_mobile_number)->first();

        if(!$driver) {
            return $this->api->json(false, 'MOBILE_NUMBER_NOT_EXIST', 'Entered mobile number does not exist in our database.');
        }



        //crate otp entry
        $otp = $this->otp->createOtpToken($driver->country_code, $driver->mobile_number);

        $contactEmail = $this->setting->get('website_contact_email');
        $messageText = "Your password reset one time password(OTP) is : {{otp_code}}. If you receives this mail or sms multiple times then contact to our support {$contactEmail}";
        $messageText = str_replace('{{otp_code}}', $otp->token, $messageText);

        //send otp via sms
        $this->otp->sendMessage($driver->country_code, $driver->mobile_number, $messageText);
        // send otp via email
        $this->email->sendCommonEmail($driver->email, $driver->fname, 'Password reset OTP', $messageText);

        return $this->api->json(true, 'PASSWORD_RESET_OTP_SENT', 'Password reset otp sent. Check you registerd mobile and email.');


    }




    /**
     * reset password
     */
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'country_code' => 'required|regex:/^[+].+$/', 
            'mobile_number' => 'required|numeric',
            'new_password' => 'required|min:6|max:100',
            'otp_code' => 'required'
        ]);

        //if validation fails
        if($validator->fails()) {
            
            $errors = [];
            foreach($validator->errors()->getMessages() as $fieldName => $msgArr) {
                $errors[$fieldName] = $msgArr[0];
            }
            return $this->api->json(false, 'VALIDATION_ERROR', 'Enter all the details', [
                'errors' => $errors
            ]);
        }


        //creating full mobile number
        $full_mobile_number = $request->country_code.$request->mobile_number;
        $driver = $this->driver->where('full_mobile_number', $full_mobile_number)->first();


        if(!$driver) {
            return $this->api->json(false, 'MOBILE_NUMBER_NOT_EXIST', 'Entered mobile number does not exist in our database.');
        }


        if($this->otp->verifyOTP($driver->country_code, $driver->mobile_number, $request->otp_code)){

            //verify mobile number
            $driver->is_mobile_number_verified = 1;
            $driver->password = Hash::make($request->new_password);
            $driver->save();

            //send password confirmamtion sms
            $this->otp->sendMessage($driver->country_code, $driver->mobile_number, 'Your password has been reset');
            // send otp via email
            $this->email->sendCommonEmail($driver->email, $driver->fname, 'Password reset', 'Your password has been reset');

            return $this->api->json(true, 'PASSWORD_RESET', 'Your password reset successful');
        }

        return $this->api->json(false, 'OTP_VERIFY_FAILED', 'Invalid otp code entered or expired.');


    }




    /** 
     * logout driver from devices or current device
     */
    public function getLogout(Request $request)
    {
        $this->api->removeAccessToken(
            $request->auth_driver->id, 
            'DRIVER', 
            $request->logout_all_devices ? false : $request->access_token
        );

        return $this->api->json(true, 'LOGOUT', "Logged out successfully");
            
    }





}
