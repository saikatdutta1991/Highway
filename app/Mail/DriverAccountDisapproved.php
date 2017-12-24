<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Driver;

class DriverAccountDisapproved extends Mailable
{
    use Queueable, SerializesModels;

    public $driver;
    public $message_text;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public function __construct(Driver $driver, $message_text)
    {
        $this->driver = $driver;
        $this->message_text = $message_text;
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
        ->subject('Your '.$this->setting->get('website_name').' account disapproved')
        ->view('emails.driver_account_disapproved')->with([
            'driver' => $this->driver,
            'message_text' => $this->message_text,
        ]);
        
    }
}
