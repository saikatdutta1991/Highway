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

class Google extends Controller
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
        config(['services.google' => [
            'client_id'     => $this->setting->get('google_client_id'),
            'client_secret' => $this->setting->get('google_secret_key'),
            'redirect' => ''
        ]]);
    }



    /**
     * retister or login user by google
     */
    public function authenticate(Request $request)
    {

        //check facebook token avail
        if(!$request->has('google_token')) {
            return $this->api->json(false, 'TOKEN_MISSING', 'Google token missing');
        }

        $this->initConfig();

        //fetch google user
        $token = $request->google_token;
        try
        {
            $sUser = \Socialite::driver('google')->userFromToken($token);

        } catch(\Exception $e) {
            \Log::info("GOOGLE_LOGIN_FETCH_USER_ERROR");
            \Log::info($e->getMessage());
            return $this->api->unknownErrResponse();
        }

        //find user by google id
        $user = $this->socialLogin->getUserBySocialLoginId($sUser->id, 'google');

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
            $user->save();

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($user->id, 'user')->access_token;

            return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
                'accesss_token' => $accessToken,
                'user' => $user
            ]);


        }


        //if user could not found by google id, check user registerd by email
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
            $user->save();

            //create social login record, because social login record is not there 
            //against this user
            $sLogin = new $this->socialLogin;
            $sLogin->entity_id = $user->id;
            $sLogin->entity_type = 'USER';
            $sLogin->social_login_id = $sUser->id;
            $sLogin->social_login_provider = 'GOOGLE';
            $sLogin->save();

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($user->id, 'user')->access_token;

            return $this->api->json(true, 'LOGIN_SUCCESS', 'You have logged in successfully.', [
                'accesss_token' => $accessToken,
                'user' => $user
            ]);
        }

        
        //user not found so register
        $user = new $this->user;

        $name = explode(' ', $sUser->getName());
        $user->fname = isset($name[0]) ? $name[0] : '';
        $user->lname = isset($name[1]) ? $name[1] : '';
        $user->email = $email;
        $user->is_email_verified = $isEmailVerified;
        $user->last_access_time = date('Y-m-d H:i:s');
        $user->last_accessed_ip = $request->ip();
        
        DB::beginTransaction();

        try {

            $user->save();

            //create social login record, because social login record is not there 
            //against this user
            $sLogin = new $this->socialLogin;
            $sLogin->entity_id = $user->id;
            $sLogin->entity_type = 'USER';
            $sLogin->social_login_id = $sUser->id;
            $sLogin->social_login_provider = 'GOOGLE';
            $sLogin->save();

            //create and save accesstoken
            $accessToken = $this->api->saveAccessToken($user->id, 'user')->access_token;

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            \Log::info('USER_GOOGLE_REGISTRATION');
            \Log::info($e->getMessage());

            return $this->api->json(false, 'REGISTER_FAILED', 'Unknown server error. Contact to service provider.');
            
        }

        
       
        return $this->api->json(true, 'REGISTER_SUCCESS', 'You have registered successfully.', [
            'accesss_token' => $accessToken,
            'user' => $user
        ]);


        
    }   




}
