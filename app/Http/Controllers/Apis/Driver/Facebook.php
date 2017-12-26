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

class Facebook extends Controller
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
     * init config 
     */
    public function initConfig()
    {
        config(['services.facebook' => [
            'client_id'     => $this->setting->get('driver_facebook_client_id'),
            'client_secret' => $this->setting->get('driver_facebook_secret_key'),
            'redirect'      => $this->setting->get('driver_facebook_redirect')
        ]]);
    }



    /**
     * retister or login driver by facebook
     */
    public function authenticate(Request $request)
    {

        //check facebook token avail
        if(!$request->has('facebook_token')) {
            return $this->api->json(false, 'TOKEN_MISSING', 'Facebook token missing');
        }

        $this->initConfig();


        //fetch facebook driver
        $token = $request->facebook_token;
        try
        {
            $sUser = \Socialite::driver('facebook')->fields([
                'first_name', 'last_name', 'email', 'gender', 'birthday'
            ])->scopes([
                'email', 'user_birthday'
            ])->userFromToken($token);

        } catch(\Exception $e) {
            \Log::info("FACEBOK_LOGIN_FETCH_DRIVER_ERROR");
            \Log::info($e->getMessage());
            return $this->api->unknownErrResponse();
        }
       
        //find driver by facebook id
        $driver = $this->socialLogin->getDriverBySocialLoginId($sUser->id, 'facebook');
        
        //if driver found means already registerd by facebook
        //so login user
        if($driver) {

            if($driver->status != 'ACTIVATED') {
                return $this->api->json(false, 'NOT_ACTIVATED', 'Your account is not activated', [
                    'reason' => $driver->reasonNotActivated()
                ]);
            }

            $driver->last_access_time = date('Y-m-d H:i:s');
            $driver->last_accessed_ip = $request->ip();

            //save profile photo
            $driver->downloadAndSavePhoto($sUser->avatar_original, 'driver_');

            //save driver timezone
            $driver->timezone = $driver->saveTimezone($request->timezone);
            
            $driver->save();


            //save device token
            $driver->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($driver->id, 'driver')->access_token;


            //don't call save on driver object
            $driver->profile_photo_url = $driver->profilePhotoUrl();

            return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
                'accesss_token' => $accessToken,
                'currency_code' => $this->setting->get('currency_code'),
                'currency_symbol' => $this->setting->get('currency_symbol'),
                'driver' => $driver
            ]);


        }
        

        //if driver could not found by facebook id, check driver registerd by email
        $email = $sUser->getEmail();
        $isEmailVerified = 0;
        if(!isset($email) || $email == '') {
        	return $this->api->json(false, 'EMAIL_ID_MISSING', 'Email id missing'); 
        } else {
            $isEmailVerified = 1;
        }
        

        //if driver found login by email
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
            $driver->downloadAndSavePhoto($sUser->avatar_original, 'driver_');


            //save driver timezone
            $driver->timezone = $driver->saveTimezone($request->timezone);

            $driver->save();

            //create social login record, because social login record is not there 
            //against this driver
            $sLogin = new $this->socialLogin;
            $sLogin->entity_id = $driver->id;
            $sLogin->entity_type = 'DRIVER';
            $sLogin->social_login_id = $sUser->id;
            $sLogin->social_login_provider = 'FACEBOOK';
            $sLogin->save();


            //save device token
            $driver->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($driver->id, 'driver')->access_token;

            //don't call save on driver object
            $driver->profile_photo_url = $driver->profilePhotoUrl();

            return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
                'accesss_token' => $accessToken,
                'currency_code' => $this->setting->get('currency_code'),
                'currency_symbol' => $this->setting->get('currency_symbol'),
                'driver' => $driver
            ]);
        }
     
      
       
        //driver not found so register
        $driver = new $this->driver;
        $driver->fname = isset($sUser->user['first_name']) ? $sUser->user['first_name'] : '';
        $driver->lname = isset($sUser->user['last_name']) ? $sUser->user['last_name'] : '';
        $driver->email = $email;
        $driver->is_email_verified = $isEmailVerified;
        $driver->last_access_time = date('Y-m-d H:i:s');
        $driver->last_accessed_ip = $request->ip();

        //save driver timezone
        $driver->timezone = $driver->saveTimezone($request->timezone);
       
        //save profile photo
        $driver->downloadAndSavePhoto($sUser->avatar_original, 'driver_');
        
        DB::beginTransaction();

        try {

            $driver->save();


            //save device token
            $driver->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create social login record, because social login record is not there 
            //against this driver
            $sLogin = new $this->socialLogin;
            $sLogin->entity_id = $driver->id;
            $sLogin->entity_type = 'DRIVER';
            $sLogin->social_login_id = $sUser->id;
            $sLogin->social_login_provider = 'FACEBOOK';
            $sLogin->save();

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($driver->id, 'driver')->access_token;

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            \Log::info('DRIVER_FACEBOOK_REGISTRATION');
            \Log::info($e->getMessage());

            return $this->api->json(false, 'REGISTER_FAILED', 'Unknown server error. Contact to service provider.');
            
        }

        //send new driver registration mail
        $this->email->sendNewDriverWelcomeEmail($driver);

        //don't call save on driver object
        $driver->profile_photo_url = $driver->profilePhotoUrl();
       
        return $this->api->json(true, 'REGISTER_SUCCESS', 'You have registered successfully.', [
            'accesss_token' => $accessToken,
            'currency_code' => $this->setting->get('currency_code'),
            'currency_symbol' => $this->setting->get('currency_symbol'),
            'driver' => $driver
        ]);


     


        
    }   




}
