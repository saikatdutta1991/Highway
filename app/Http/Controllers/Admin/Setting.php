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




}
