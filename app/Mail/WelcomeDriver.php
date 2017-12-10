<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Models\Driver;

class WelcomeDriver extends Mailable
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
       
        return $this->from(
            $this->setting->get('email_support_from_address'), 
            $this->setting->get('email_from_name')
        )
        ->subject('Welcome to ' . $this->setting->get('website_name'). ' :)')
        ->view('emails.welcome_driver')->with([
            'driver' => $this->driver,
            'website_logo_url' => $this->setting->websiteLogoUrl(),
            'website_name' => $this->setting->get('website_name'),
            'website_address' => $this->setting->get('website_address'),
            'website_contact_number' => $this->setting->get('website_contact_number'),
            'website_contact_email' => $this->setting->get('website_contact_email'),
        ]);
    }
}
