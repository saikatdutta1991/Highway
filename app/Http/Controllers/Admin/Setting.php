<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting as Set;
use Validator;


class Setting extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Set $setting, Api $api)
    {
        $this->setting = $setting;
        $this->api = $api;
    }




    /**
     * show seo settings page
     */
    public function showSeoSetting()
    {
        return view('admin.seo_management');
    }



    /**
     * save seo settings
     */
    public function saveSeoSetting(Request $request)
    {
        $this->setting->set('seo_title', trim($request->seo_title));
        $this->setting->set('seo_keywords', trim($request->seo_keywords));
        $this->setting->set('seo_description', trim($request->seo_description));
        return $this->api->json(true, 'SEO_SETTINGS_SAVED', 'SEO settings saved');
    }







    /**
     * shows email settings view
     */
    public function showEmailSetting()
    {
        $setting = $this->setting;
        return view('admin.email_settings', compact('setting'));
    }



    /**
     * save email settings
     */
    public function saveEmailSettings(Request $request)
    {
        //$this->setting->syncWithConfigFile();


        //get mail driver from request
        $driver = isset($request->email_settings['mail_driver']) ? $request->email_settings['mail_driver'] : '';
        $emailSettings = $request->email_settings;

        //if mail driver null then make mail send deactivated
        if($driver == '') {
            $this->setting->set('is_mail_send_activated', 'false');

            //remove mail driver form array because driver null
            unset($emailSettings['mail_driver']);
        }
        //mail send activated
        else {
            $this->setting->set('is_mail_send_activated', 'true');
        }

        //save all email settings to db
        foreach($emailSettings as $key => $value) {
            $this->setting->set($key, trim($value));
        }

        return $this->api->json(true, 'EMAIL_SETTINGS_SAVED', 'Email settings saved');

    }



    /**
     * send email for testing
     */
    public function testEmail(Request $request)
    {
        $toEmail = trim($request->to_email);
        $body = $request->body;  
        $subject = $request->subject; 

        if($this->setting->get('is_mail_send_activated') != 'true') {
            return $this->api->json(false, "MAIL_DRIVER_NO_SET", 'Mail driver not set yet');
        }


        try {

            \Mail::raw($body, function($message) use($toEmail,$subject) {
                $message->to($toEmail)->subject($subject);
                $message->from(
                    $this->setting->get('email_support_from_address'), 
                    $this->setting->get('email_from_name')
                );
            });

        } catch(\Exception $e) {
            return $this->api->json(false, "MAIL_SEND_ERROR", 'Mail send failed', [
                'error_message' => $e->getMessage()
            ]);
        }

        
        return $this->api->json(true, "MAIL_SEND_SUCCESS", 'Mail send successful');

    }





    /**
     * show sms settings
     */
    public function showSmsSetting()
    {
        $setting = $this->setting;
        return view('admin.sms_settings', compact('setting'));
    }





    /**
     * save sms settings
     */
    public function saveSmsSetting(Request $request)
    {

        if($request->has('sms_provider')) {
            $this->setting->set('sms_provider', trim($request->sms_provider));
        }

        //if twilio_sid is there save twilio credentials
        if($request->has('twilio_sid') && $request->twilio_sid != '') {
            $this->setting->set('twilio_sid', trim($request->twilio_sid));
            $this->setting->set('twilio_token', trim($request->twilio_token));
            $this->setting->set('twilio_from', trim($request->twilio_from));
        }

        //if msg91 sender id is there , save msg91 credentials 
        if($request->has('msg91_sender_id') && $request->msg91_sender_id != '') {
            $this->setting->set('msg91_sender_id', trim($request->msg91_sender_id));
            $this->setting->set('msg91_auth_key', trim($request->msg91_auth_key));
        }

        return $this->api->json(true, 'SMS_SETTINGS_SAVED', 'Sms settings saved');

    }



    /**
     * send test sms
     */
    public function testSms(Request $request)
    {
        $toNumber = trim($request->to_number);
        list($countryCode, $mobileNo) = explode('-', $toNumber);
        $message = 'Test sms from '.$this->setting->get('website_name');
        $err = null;
        $success = app('App\Repositories\Otp')->sendMessage($countryCode, $mobileNo, $message, $err);
        
        if($success) {
            return $this->api->json(true, "SMS_SEND_SUCCESS", 'Sms send successful');
        } else {
            return $this->api->json(false, "SMS_SEND_ERROR", 'Sms send failed', [
                'error_message' => $err . ' --credentials might be wrong'
            ]);
        }

    }




    /**
     * show firebase setting
     */
    public function showFirebaseSetting()
    {
        $firebaseSenderId = $this->setting->get('firebase_cloud_messaging_sender_id');
        $firebaseServerKey = $this->setting->get('firebase_cloud_messaging_server_key');
        return view('admin.firebase_settings', compact('firebaseSenderId', 'firebaseServerKey'));
    }


    /**
     * save firebase settings
     */
    public function saveFirebaseSetting(Request $request)
    {
        $this->setting->set('firebase_cloud_messaging_sender_id', trim($request->firebase_cloud_messaging_sender_id));
        $this->setting->set('firebase_cloud_messaging_server_key', trim($request->firebase_cloud_messaging_server_key));
        return $this->api->json(true, 'FIREBASE_SETTINGS_SAVED', 'Firebase settings saved');
    }



    /**
     * show facbeook setting
     */
    public function showFacebookSetting()
    {
        $userFacebookClientId = $this->setting->get('user_facebook_client_id');
        $userFacebookSecretKey = $this->setting->get('user_facebook_secret_key');
        $driverFacebookClientId = $this->setting->get('driver_facebook_client_id');
        $driverFacebookSecretKey = $this->setting->get('driver_facebook_secret_key');
        return view('admin.facebook_settings', compact(
            'userFacebookClientId', 'userFacebookSecretKey',
            'driverFacebookClientId', 'driverFacebookSecretKey'
        ));
    }


    /**
     * save facbeook settings
     */
    public function saveFacebookSetting(Request $request)
    {
        $this->setting->set('user_facebook_client_id', trim($request->user_facebook_client_id));
        $this->setting->set('user_facebook_secret_key', trim($request->user_facebook_secret_key));
        $this->setting->set('driver_facebook_client_id', trim($request->driver_facebook_client_id));
        $this->setting->set('driver_facebook_secret_key', trim($request->driver_facebook_secret_key));
        return $this->api->json(true, 'FACEBOOK_SETTINGS_SAVED', 'Facebook settings saved');
    }



    /**
     * show google setting
     */
    public function showGoogleSetting()
    {
        $userAndroidGoogleAuthClientId = $this->setting->get('user_android_google_login_client_id');
        $userIosGoogleAuthClientId = $this->setting->get('user_ios_google_login_client_id');
        $driverAndroidGoogleAuthClientId = $this->setting->get('driver_android_google_login_client_id');
        $driverIosGoogleAuthClientId = $this->setting->get('driver_ios_google_login_client_id');
        $googleMapApiKey = $this->setting->get('google_maps_api_key');
        return view('admin.google_settings', compact(
            'userAndroidGoogleAuthClientId', 'userIosGoogleAuthClientId',
            'driverAndroidGoogleAuthClientId', 'driverIosGoogleAuthClientId', 'googleMapApiKey'
        ));
    }


    /**
     * save google settings
     */
    public function saveGoogleSetting(Request $request)
    {
        $this->setting->set('user_android_google_login_client_id', trim($request->user_android_google_login_client_id));
        $this->setting->set('user_ios_google_login_client_id', trim($request->user_ios_google_login_client_id));
        $this->setting->set('driver_android_google_login_client_id', trim($request->driver_android_google_login_client_id));
        $this->setting->set('driver_ios_google_login_client_id', trim($request->driver_ios_google_login_client_id));
        return $this->api->json(true, 'GOOGLE_SETTINGS_SAVED', 'Google auth settings saved');
    }


    /**
     * save google map key
     */
    public function saveGoogleMapKey(Request $request)
    {
        $this->setting->set('google_maps_api_key', trim($request->google_maps_api_key));
        $this->setting->set('google_maps_api_key_booking_track', trim($request->google_maps_api_key_booking_track));
        return $this->api->json(true, 'GOOGLE_MAPS_API_KEYS_SAVED', 'Google map keys saved');
    }



    /**
     * show general settings
     */
    public function showGeneralSetting()
    {
        $setting = $this->setting;
        $timezones = config('timezones');
        return view('admin.general_settings', compact('setting', 'timezones'));
    }


    /**
     * save website general settings
     */
    public function saveGeneralSettings(Request $request)
    {
        $this->setting->set('website_name', trim($request->website_name));
        $this->setting->set('android_driver_apphash_sms', trim($request->android_driver_apphash_sms));
        $this->setting->set('android_user_apphash_sms', trim($request->android_user_apphash_sms));
        $this->setting->set('website_title', trim($request->website_title));
        $this->setting->set('website_company_name', trim($request->website_company_name));
        $this->setting->set('website_copyright', trim($request->website_copyright));
        $this->setting->set('default_timezone', trim($request->default_timezone));
        $this->setting->set('website_contact_number', trim($request->website_contact_number));
        $this->setting->set('website_contact_email', trim($request->website_contact_email));
        $this->setting->set('website_address', trim($request->website_address));
        $this->setting->set('default_user_driver_timezone', trim($request->default_user_driver_timezone));
        $this->setting->set('currency_code', explode('-', $request->currency)[0]);
        $this->setting->set('currency_symbol', explode('-', $request->currency)[1]);
        $this->setting->set('android_user_app_package', trim($request->android_user_app_package));
        
        return $this->api->json(true, 'GENERAL_WEBSITE_SETTINGS_SAVED', 'Website settings saved');
    }



    /**
     * save website logo
     */
    public function saveWebsiteLogo(Request $request)
    {
        $url = $this->setting->saveWebsiteLogo($request->photo);
        return $this->api->json(true, 'PHOTO_SAVED', 'Website logo saved', [
            'logo_url' => $url,
        ]);
    }


    /**
     * upload and save website favicon
     */
    public function saveWebsiteFavicon(Request $request)
    {
        $url = $this->setting->saveWebsiteFavicon($request->photo);
        return $this->api->json(true, 'PHOTO_SAVED', 'Website favicon saved', [
            'favicon_url' => $url
        ]);
    }





    /**
     * show razorpay api key settings
     */
    public function showRazorpaySetting(Request $request)
    {
        $RAZORPAY_MERCHANT_ID = $this->setting->get('RAZORPAY_MERCHANT_ID');
        $RAZORPAY_API_KEY = $this->setting->get('RAZORPAY_API_KEY');
        $RAZORPAY_API_SECRET = $this->setting->get('RAZORPAY_API_SECRET');
        return view('admin.razorpay_settings', compact('RAZORPAY_MERCHANT_ID', 'RAZORPAY_API_KEY', 'RAZORPAY_API_SECRET'));
    }

    /**
     * save razorpay settings
     */
    public function saveRazorpaySetting(Request $request)
    {
        $this->setting->set('RAZORPAY_MERCHANT_ID', trim($request->razorpay_merchant_id));
        $this->setting->set('RAZORPAY_API_KEY', trim($request->razorpay_api_key));
        $this->setting->set('RAZORPAY_API_SECRET', trim($request->razorpay_api_secret));
        return $this->api->json(true, 'RAZORPAY_SAVED', 'Razorpay api keys saved');
    }


}
