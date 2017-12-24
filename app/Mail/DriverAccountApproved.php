<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Driver;

class DriverAccountApproved extends Mailable
{
    use Queueable, SerializesModels;

    public $driver;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->setting = app('App\Models\Setting');
        $this->utillRepo = app('UtillRepo');
       
        return $this->from(
            $this->setting->get('email_support_from_address'), 
            $this->setting->get('email_from_name')
        )
        ->subject($this->setting->get('website_name').' account approved')
        ->view('emails.driver_account_approved')->with([
            'driver' => $this->driver
        ]);
        
    }
}
