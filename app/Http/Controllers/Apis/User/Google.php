<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use App\Repositories\Email;
use Hash;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\SocialLogin;
use Validator;
use App\Models\User;

class Google extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Email $email, Api $api, User $user, SocialLogin $socialLogin, Setting $setting)
    {
        $this->email = $email;
        $this->api = $api;
        $this->user = $user;
        $this->socialLogin = $socialLogin;
        $this->setting = $setting;
    }




    /**
     * retister or login user by google
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

        
        //fetch google user
        $token = $request->google_token;
        try
        {
            if($request->auth_device_type == 'ANDROID') {
                $clientId = $this->setting->get('user_android_google_login_client_id');
            } else if($request->auth_device_type == 'IOS') {
                $clientId = $this->setting->get('user_ios_google_login_client_id');
            }

            $client = new \Google_Client(['client_id' => $clientId]);

            $payload = $client->verifyIdToken($token);
            if($payload) {
               $sUser = json_decode(json_encode($payload));
            } else {
                throw new \Exception('Invalid client id token.');
            }

        } catch(\Exception $e) {
            \Log::info("GOOGLE_LOGIN_FETCH_USER_ERROR");
            \Log::info($e->getMessage());
            return $this->api->unknownErrResponse();
        }

        //find user by google id
        $user = $this->socialLogin->getUserBySocialLoginId($sUser->sub, 'google');

        //if user found means already registerd by facebook
        //so login user
        if($user) {

            if($user->status != 'ACTIVATED') {
                return $this->api->json(false, 'NOT_ACTIVATED', 'User not activated', [
                    'reason' => $user->reasonNotActivated()
                ]);
            }

            $user->last_access_time = date('Y-m-d H:i:s');
            $user->last_accessed_ip = $request->ip();
            $user->saveTimezone($request->timezone, false);
            $user->save();

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


        //if user could not found by google id, check user registerd by email
        $isEmailVerified = 0;
        if(!isset($sUser->email) || $sUser->email == '') {
        	return $this->api->json(false, 'EMAIL_ID_MISSING', 'Email id missing'); 
        } else {
            $email = $sUser->email;
            $isEmailVerified = 1;
        }


        //if user found login
        $user = $this->user->where('email', $email)->first();
        if($user) {
            if($user->status != 'ACTIVATED') {
                return $this->api->json(false, 'NOT_ACTIVATED', 'User not activated', [
                    'reason' => $user->reasonNotActivated()
                ]);
            }

            $user->last_access_time = date('Y-m-d H:i:s');
            $user->last_accessed_ip = $request->ip();
            $user->saveTimezone($request->timezone, false);
            $user->save();

            //create social login record, because social login record is not there 
            //against this user
            $sLogin = new $this->socialLogin;
            $sLogin->entity_id = $user->id;
            $sLogin->entity_type = 'USER';
            $sLogin->social_login_id = $sUser->sub;
            $sLogin->social_login_provider = 'GOOGLE';
            $sLogin->save();

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

        
        //user not found so register
        $user = new $this->user;

        $name = explode(' ', $sUser->name);
        $user->fname = isset($name[0]) ? $name[0] : '';
        $user->lname = isset($name[1]) ? $name[1] : '';
        $user->email = $email;
        $user->is_email_verified = $isEmailVerified;
        $user->last_access_time = date('Y-m-d H:i:s');
        $user->last_accessed_ip = $request->ip();
        $user->saveTimezone($request->timezone, false);
        
        DB::beginTransaction();

        try {

            $user->save();

            //create social login record, because social login record is not there 
            //against this user
            $sLogin = new $this->socialLogin;
            $sLogin->entity_id = $user->id;
            $sLogin->entity_type = 'USER';
            $sLogin->social_login_id = $sUser->sub;
            $sLogin->social_login_provider = 'GOOGLE';
            $sLogin->save();

            //save device token
            $user->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($user->id, 'user')->access_token;

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            \Log::info('USER_GOOGLE_REGISTRATION');
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




}
