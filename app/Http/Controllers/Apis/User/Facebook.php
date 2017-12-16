<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Hash;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\SocialLogin;
use Validator;
use App\Models\User;

class Facebook extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, User $user, SocialLogin $socialLogin, Setting $setting)
    {
        $this->api = $api;
        $this->user = $user;
        $this->socialLogin = $socialLogin;
        $this->setting = $setting;
    }



    /**
     * init config 
     */
    public function initConfig()
    {
        config(['services.facebook' => [
            'client_id'     => $this->setting->get('user_facebook_client_id'),
            'client_secret' => $this->setting->get('user_facebook_secret_key'),
            'redirect'      => $this->setting->get('user_facebook_redirect')
        ]]);
    }



    /**
     * retister or login user by facebook
     */
    public function authenticate(Request $request)
    {

        //check facebook token avail
        if(!$request->has('facebook_token')) {
            return $this->api->json(false, 'TOKEN_MISSING', 'Facebook token missing');
        }

        $this->initConfig();


        //fetch facebook user
        $token = $request->facebook_token;
        try
        {
            $sUser = \Socialite::driver('facebook')->fields([
                'first_name', 'last_name', 'email', 'gender', 'birthday'
            ])->scopes([
                'email', 'user_birthday'
            ])->userFromToken($token);

        } catch(\Exception $e) {
            \Log::info("FACEBOK_LOGIN_FETCH_USER_ERROR");
            \Log::info($e->getMessage());
            return $this->api->unknownErrResponse();
        }
       

        //find user by facebook id
        $user = $this->socialLogin->getUserBySocialLoginId($sUser->id, 'facebook');
        
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
            
            //save user timezone
            $user->timezone = $user->saveTimezone($request->timezone);

            $user->save();


            //save device token
            $user->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($user->id, 'user')->access_token;

            return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
                'accesss_token' => $accessToken,
                'user' => $user
            ]);


        }
        

        //if user could not found by facebook id, check user registerd by email
        $email = $sUser->getEmail();
        $isEmailVerified = 0;
        if(!isset($email) || $email == '') {
        	return $this->api->json(false, 'EMAIL_ID_MISSING', 'Email id missing'); 
        } else {
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

            //save user timezone
            $user->timezone = $user->saveTimezone($request->timezone);

            $user->save();

            //create social login record, because social login record is not there 
            //against this user
            $sLogin = new $this->socialLogin;
            $sLogin->entity_id = $user->id;
            $sLogin->entity_type = 'USER';
            $sLogin->social_login_id = $sUser->id;
            $sLogin->social_login_provider = 'FACEBOOK';
            $sLogin->save();


            //save device token
            $user->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($user->id, 'user')->access_token;

            return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
                'accesss_token' => $accessToken,
                'user' => $user
            ]);
        }
        

       
        //user not found so register
        $user = new $this->user;
        $user->fname = isset($sUser->user['first_name']) ? $sUser->user['first_name'] : '';
        $user->lname = isset($sUser->user['last_name']) ? $sUser->user['last_name'] : '';
        $user->email = $email;
        $user->is_email_verified = $isEmailVerified;
        $user->last_access_time = date('Y-m-d H:i:s');
        $user->last_accessed_ip = $request->ip();
        //save user timezone
        $user->timezone = $user->saveTimezone($request->timezone);
        
        DB::beginTransaction();

        try {

            $user->save();

            //create social login record, because social login record is not there 
            //against this user
            $sLogin = new $this->socialLogin;
            $sLogin->entity_id = $user->id;
            $sLogin->entity_type = 'USER';
            $sLogin->social_login_id = $sUser->id;
            $sLogin->social_login_provider = 'FACEBOOK';
            $sLogin->save();

            //save device token
            $user->addOrUpdateDeviceToken($request->device_type, $request->device_token);

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($user->id, 'user')->access_token;

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            \Log::info('USER_FACEBOOK_REGISTRATION');
            \Log::info($e->getMessage());

            return $this->api->json(false, 'REGISTER_FAILED', 'Unknown server error. Contact to service provider.');
            
        }

        
       
        return $this->api->json(true, 'REGISTER_SUCCESS', 'You have registered successfully.', [
            'accesss_token' => $accessToken,
            'user' => $user
        ]);


     


        
    }   




}
