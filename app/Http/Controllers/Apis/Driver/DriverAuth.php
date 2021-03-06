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
use App\Models\DeviceToken;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;


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
            'vtypes' => $this->vehicleType->allTypes()->where('is_activated', true)
        ]);
    }





    public function doRegister(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'fname' => 'required|max:128',
            'lname' => 'required|max:128',
            'email' => 'required|email|max:128|unique:drivers,email',
            'password' => 'required|min:6|max:100',
            'country_code' => 'required|regex:/^[+].+$/', 
            'mobile_number' => [ 
                "required_with:country_code", 
                "numeric",
                Rule::unique('drivers')->where( function ($query) use( $request ) {
                    return $query->where( 'country_code', $request->country_code );
                })
            ],
            'photo' => 'required|image|mimes:jpg,jpeg,png',
            'vehicle_type' => 'required_if:ready_to_get_hired,0|in:'.implode(',', $this->vehicleType->allCodes()),
            'vehicle_number' => 'required_if:ready_to_get_hired,0',
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
            'skipBankData' => 'required|boolean',
            'bank_name' => 'required_if:skipBankData,==,0|max:128',
            'bank_account_holder_name' => 'required_if:skipBankData,==,0|max:256',
            'bank_ifsc_code' => 'required_if:skipBankData,==,0|max:50',
            'bank_account_number' => 'required_if:skipBankData,==,0|max:50',
            'bank_extra_info' => 'min:5|max:256',
            'manual_transmission' => 'required|boolean',
            'automatic_transmission' => 'required|boolean',
            'ready_to_get_hired' => 'required|boolean'
        ]);
       


        if($validator->fails()) {

            $messages = [];
            foreach($validator->errors()->getMessages() as $attr => $errArray) {
                $messages[$attr] = $errArray[0];
            }

            return $this->api->json(false, 'VALIDATION_ERROR', 'Enter all the mandatory fields', $messages);
        }
        

        //check mobile number already exists
        // $full_mobile_number = $request->country_code.$request->mobile_number;
        // if($this->driver->where('full_mobile_number', $full_mobile_number)->exists()) {
        //     return $this->api->json(false, 'MOBILE_NUMBER_DUPLICATE', 'Mobile number is already registered. Try another mobile number.');
        // }


        //check email number already exists
        // if($this->driver->where('email', $request->email)->exists()) {
        //     return $this->api->json(false, 'EMAIL_DUPLICATE', 'Email is already registered. Try another email address.');
        // }


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
        $driver->vehicle_type = $request->vehicle_type ?: '';
        $driver->vehicle_number = $request->vehicle_number ? strtoupper($request->vehicle_number) : '';
        $driver->manual_transmission = $request->manual_transmission;
        $driver->automatic_transmission = $request->automatic_transmission;
        $driver->ready_to_get_hired = $request->ready_to_get_hired;

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
        if($request->skipBankData) {
            $bank = new DriverBank;
            $bank->bank_name = '';
            $bank->account_holder_name = '';
            $bank->ifsc_code = '';
            $bank->account_number = '';
            $bank->extra_info = '';
        } else {
            $bank = new DriverBank;
            $bank->bank_name = ucwords($request->bank_name);
            $bank->account_holder_name = ucwords($request->bank_account_holder_name);
            $bank->ifsc_code = ucfirst($request->bank_ifsc_code);
            $bank->account_number = strtoupper($request->bank_account_number);
            $bank->extra_info = $request->bank_extra_info ?: '';
        }


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
        $driver->service_name = $this->vehicleType->allTypes()->where('code', $driver->vehicle_type)->first()['name'];


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

        $driver->service_name = $this->vehicleType->allTypes()->where('code', $driver->vehicle_type)->first()['name'];

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
        // remove access tokens
        $this->api->removeAccessToken($request->auth_driver->id, 'DRIVER', false);

        // remove push tokens
        DeviceToken::where("entity_id", $request->auth_driver->id)->where("entity_type", "DRIVER")->forceDelete();

        // make driver unavailable
        Driver::where("id", $request->auth_driver->id)->update([ "is_available" => false ]);

        Api::forgetDriverTokensCache($request->auth_driver->id);

        return $this->api->json(true, 'LOGOUT', "Logged out successfully");
            
    }





}
