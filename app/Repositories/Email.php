<?php

namespace App\Repositories;

use Mail;
use App\Models\Setting;
use App\Mail\WelcomeUser;
use App\Mail\WelcomeDriver;
use App\Mail\DriverAccountApproved;
use App\Mail\DriverAccountDisapproved;
use App\Mail\CommonTemplate;

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
     * send user trip invoice
     */
    public function sendUserTripInvoiceEmail($userBooking)
    {
        //if email send is not active from admin panel return from here
        if(!$this->isEmailSendActive()) {
            \Log::info('EMAIL_SEND_NOT_ACTIVATED');
            return false;
        }

        try {
            
            $resCode = Mail::to($userBooking->user->email)->queue(new \App\Mail\TripInvoice($userBooking));
            \Log::info('MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);
        
        } catch(\Exception $e) {
            \Log::info('MAIL PUSHED TO QUEUE ERROR :');
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
            return false;
        }
        
        return true;


    }



    /**
     * send user ride request invoice
     */
    public function sendUserRideRequestInvoiceEmail($rideRequest)
    {
        //if email send is not active from admin panel return from here
        if(!$this->isEmailSendActive()) {
            \Log::info('EMAIL_SEND_NOT_ACTIVATED');
            return false;
        }

        try {
            
            $resCode = Mail::to($rideRequest->user->email)->queue(new \App\Mail\RideRequestInvoice($rideRequest));
            \Log::info('MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);
        
        } catch(\Exception $e) {
            \Log::info('MAIL PUSHED TO QUEUE ERROR :');
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
            return false;
        }
        
        return true;
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
            $resCode = Mail::to($user->email)->queue(new WelcomeUser($user));
            \Log::info('MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);
        
        } catch(\Exception $e) {
            \Log::info('MAIL PUSHED TO QUEUE ERROR :');
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
            return false;
        }

        return true;
        
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
            return false;
        }

        return true;
        
    }




    /**
     * send driver account approved email
     */    
    public function sendDriverAccountApproved($driver)
    {
        //if email send is not active from admin panel return from here
        if(!$this->isEmailSendActive()) {
            \Log::info('EMAIL_SEND_NOT_ACTIVATED');
            return false;
        }

        try {
            $resCode = Mail::to($driver->email)->queue(new DriverAccountApproved($driver));
            \Log::info('MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);
        
        } catch(\Exception $e) {
            \Log::info('MAIL PUSHED TO QUEUE ERROR :');
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
            return false;
        }

        return true;
        
    }





    /**
     * send driver account disapproved email
     */    
    public function sendDriverAccountDisapproved($driver, $message)
    {
        //if email send is not active from admin panel return from here
        if(!$this->isEmailSendActive()) {
            \Log::info('EMAIL_SEND_NOT_ACTIVATED');
            return false;
        }

        try {
            $resCode = Mail::to($driver->email)->queue(new DriverAccountDisapproved($driver, $message));
            \Log::info('MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);
        
        } catch(\Exception $e) {
            \Log::info('MAIL PUSHED TO QUEUE ERROR :');
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
            return false;
        }

        return true;
        
    }




    /**
     * send driver password reset by admin
     */
    public function sendDriverPasswordResetAdmin($driver, $newPassword)
    {
        //if email send is not active from admin panel return from here
        if(!$this->isEmailSendActive()) {
            \Log::info('EMAIL_SEND_NOT_ACTIVATED');
            return false;
        }

        try {
            $resCode = Mail::to($driver->email)->queue(new \App\Mail\DriverPasswordResetAdmin($driver, $newPassword));
            \Log::info('MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);
        
        } catch(\Exception $e) {
            \Log::info('MAIL PUSHED TO QUEUE ERROR :');
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
            return false;
        }

        return true;
        
    }




    /**
     * send user password reset by admin
     */
    public function sendUserPasswordResetAdmin($user, $newPassword)
    {
        //if email send is not active from admin panel return from here
        if(!$this->isEmailSendActive()) {
            \Log::info('EMAIL_SEND_NOT_ACTIVATED');
            return false;
        }

        try {
            $resCode = Mail::to($user->email)->queue(new \App\Mail\UserPasswordResetAdmin($user, $newPassword));
            \Log::info('MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);
        
        } catch(\Exception $e) {
            \Log::info('MAIL PUSHED TO QUEUE ERROR :'.$user->email);
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
            return false;
        }

        return true;
        
    }



    /**
     * send common email
     */
    public function sendCommonEmail($toEmail, $welcomename, $subject, $messageBody)
    {
         //if email send is not active from admin panel return from here
        if(!$this->isEmailSendActive()) {
            \Log::info('EMAIL_SEND_NOT_ACTIVATED');
            return false;
        }

        try {
            $resCode = Mail::to($toEmail)->queue(new CommonTemplate($welcomename, $subject, $messageBody));
            \Log::info('COMMON MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);
        
        } catch(\Exception $e) {
            \Log::info('COMMON MAIL PUSHED TO QUEUE ERROR :'.$toEmail);
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
            return false;
        }

        return true;
    }



}