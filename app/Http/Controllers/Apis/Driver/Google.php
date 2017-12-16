<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\SocialLogin;
use Validator;
use App\Models\Driver;

class Google extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, Driver $driver, SocialLogin $socialLogin, Setting $setting)
    {
        $this->api = $api;
        $this->driver = $driver;
        $this->socialLogin = $socialLogin;
        $this->setting = $setting;
    }



    /**
     * retister or login driver by google
     */
    public function authenticate(Request $request)
    {

        //auth device type required for android or ios google client id
        if(!$request->has('auth_device_type') || !in_array($request->auth_device_type, ['ANDROID', 'IOS']) ) {
            return $this->api->json(false, 'AUTH_DEVICE_TYPE_MISSING', 'Auth device type is missing.');
        }

        //check facebook token avail
        if(!$request->has('google_token')) {
            return $this->api->json(false, 'TOKEN_MISSING', 'Google token missing');
        }


        //fetch google driver
        $token = $request->google_token;
        try
        {
            if($request->auth_device_type == 'ANDROID') {
                $clientId = $this->setting->get('driver_android_google_login_client_id');
            } else if($request->auth_device_type == 'IOS') {
                $clientId = $this->setting->get('driver_ios_google_login_client_id');
            }

            $client = new \Google_Client(['client_id' => $clientId]);

            $payload = $client->verifyIdToken($token);
            if($payload) {
               $sUser = json_decode(json_encode($payload));
            } else {
                throw new \Exception('Invalid client id token.');
            }

        } catch(\Exception $e) {
            \Log::info("GOOGLE_LOGIN_FETCH_DRIVER_ERROR");
            \Log::info($e->getMessage());
            return $this->api->unknownErrResponse();
        }

        //find driver by google id
        $driver = $this->socialLogin->getDriverBySocialLoginId($sUser->sub, 'google');

        //if driver found means already registerd by facebook
        //so login driver
        if($driver) {

            if($driver->status != 'ACTIVATED') {
                return $this->api->json(false, 'NOT_ACTIVATED', 'Your account is not activated', [
                    'reason' => $driver->reasonNotActivated()
                ]);
            }

            $driver->last_access_time = date('Y-m-d H:i:s');
            $driver->last_accessed_ip = $request->ip();

            //save profile photo
            if(isset($sUser->picture)) {
                $driver->downloadAndSavePhoto($sUser->picture, 'driver_');
            }

            //save driver timezone
            $driver->saveTimezone($request->timezone);

            $driver->save();

            //save device token
            $driver->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($driver->id, 'driver')->access_token;

            //don't call save on driver object
            $driver->profile_photo_url = $driver->profilePhotoUrl();

            return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
                'accesss_token' => $accessToken,
                'driver' => $driver
            ]);


        }


        //if driver could not found by google id, check driver registerd by email
        $isEmailVerified = 0;
        if(!isset($sUser->email) || $sUser->email == '') {
        	return $this->api->json(false, 'EMAIL_ID_MISSING', 'Email id missing'); 
        } else {
            $email = $sUser->email;
            $isEmailVerified = 1;
        }


        //if driver found login
        $driver = $this->driver->where('email', $email)->first();
        if($driver) {
            if($driver->status != 'ACTIVATED') {
                return $this->api->json(false, 'NOT_ACTIVATED', 'Your account is not activated', [
                    'reason' => $driver->reasonNotActivated()
                ]);
            }

            $driver->last_access_time = date('Y-m-d H:i:s');
            $driver->last_accessed_ip = $request->ip();

            //save profile photo
            if(isset($sUser->picture)) {
                $driver->downloadAndSavePhoto($sUser->picture, 'driver_');
            }

            //save driver timezone
            $driver->saveTimezone($request->timezone);

            $driver->save();

            //create social login record, because social login record is not there 
            //against this driver
            $sLogin = new $this->socialLogin;
            $sLogin->entity_id = $driver->id;
            $sLogin->entity_type = 'DRIVER';
            $sLogin->social_login_id = $sUser->sub;
            $sLogin->social_login_provider = 'GOOGLE';
            $sLogin->save();

            //save device token
            $driver->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($driver->id, 'driver')->access_token;

            //don't call save on driver object
            $driver->profile_photo_url = $driver->profilePhotoUrl();

            return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
                'accesss_token' => $accessToken,
                'driver' => $driver
            ]);
        }

        
        //driver not found so register
        $driver = new $this->driver;

        $name = explode(' ', $sUser->name);
        $driver->fname = isset($name[0]) ? $name[0] : '';
        $driver->lname = isset($name[1]) ? $name[1] : '';
        $driver->email = $email;
        $driver->is_email_verified = $isEmailVerified;
        $driver->last_access_time = date('Y-m-d H:i:s');
        $driver->last_accessed_ip = $request->ip();

        //save driver timezone
        $driver->saveTimezone($request->timezone);

        //save profile photo
        if(isset($sUser->picture)) {
            $driver->downloadAndSavePhoto($sUser->picture, 'driver_');
        }
        
        DB::beginTransaction();

        try {

            $driver->save();

            //create social login record, because social login record is not there 
            //against this driver
            $sLogin = new $this->socialLogin;
            $sLogin->entity_id = $driver->id;
            $sLogin->entity_type = 'DRIVER';
            $sLogin->social_login_id = $sUser->sub;
            $sLogin->social_login_provider = 'GOOGLE';
            $sLogin->save();


            //save device token
            $driver->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($driver->id, 'driver')->access_token;

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            \Log::info('DRIVER_GOOGLE_REGISTRATION');
            \Log::info($e->getMessage());

            return $this->api->json(false, 'REGISTER_FAILED', 'Unknown server error. Contact to service provider.');
            
        }

        //send new driver registration mail
        $this->email->sendNewDriverWelcomeEmail($driver);
        
        //don't call save on driver object
        $driver->profile_photo_url = $driver->profilePhotoUrl();
       
        return $this->api->json(true, 'REGISTER_SUCCESS', 'You have registered successfully.', [
            'accesss_token' => $accessToken,
            'driver' => $driver
        ]);


        
    }   




}
