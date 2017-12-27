<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Driver;

class DriverPasswordResetAdmin extends Mailable
{
    use Queueable, SerializesModels;

    public $driver;
    public $newPassword;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public function __construct(Driver $driver, $newPassword)
    {
        $this->driver = $driver;
        $this->newPassword = $newPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->setting = app('App\Models\Setting');
       
        return $this->from(
            $this->setting->get('email_support_from_address'), 
            $this->setting->get('email_from_name')
        )
        ->subject($this->setting->get('website_name').' Password Reset by Admin')
        ->view('emails.driver_password_reset_by_admin')->with([
            'driver' => $this->driver,
            'newPassword' => $this->newPassword
        ]);
        
    }
}
