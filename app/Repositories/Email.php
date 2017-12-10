<?php

namespace App\Repositories;

use Mail;
use App\Models\Setting;
use App\Mail\WelcomeUser;
use App\Mail\WelcomeDriver;

class Email 
{

	public function __construct(Setting $setting)
	{
		$this->setting = $setting;
    }
    

    /**
     * reutrns mail send activated or not
     */
    public function isEmailSendActive()
    {
        return ($this->setting->get('is_mail_send_activated') == 'true');
    }



    /**
     * send new user registration email
     */
    public function sendNewUserWelcomeEmail($user)
    {
        //if email send is not active from admin panel return from here
        if(!$this->isEmailSendActive()) {
            \Log::info('EMAIL_SEND_NOT_ACTIVATED');
            return false;
        }

        try {
            $resCode = Mail::to($user->email)->send(new WelcomeUser($user));
            \Log::info('MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);
        
        } catch(\Exception $e) {
            \Log::info('MAIL PUSHED TO QUEUE ERROR :');
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
        }
        
    }



    /**
     * send new driver registration email
     */
    public function sendNewDriverWelcomeEmail($driver)
    {
        //if email send is not active from admin panel return from here
        if(!$this->isEmailSendActive()) {
            \Log::info('EMAIL_SEND_NOT_ACTIVATED');
            return false;
        }

        try {
            $resCode = Mail::to($driver->email)->queue(new WelcomeDriver($driver));
            \Log::info('MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);
        
        } catch(\Exception $e) {
            \Log::info('MAIL PUSHED TO QUEUE ERROR :');
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
        }
        
    }






}