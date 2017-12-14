<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\RideRequest;
use App\Models\RideRequestInvoice as Invoice;
use App\Models\Driver;
use App\Models\User;

class RideRequestInvoice extends Mailable
{
    use Queueable, SerializesModels;


    public $user;
    public $driver;
    public $rideRequest;
    public $invoice;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public function __construct(RideRequest $rideRequest, User $user, Driver $driver, Invoice $invoice)
    {
        $this->user = $user;
        $this->driver = $driver;
        $this->rideRequest = $rideRequest;
        $this->invoice = $invoice;
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
        ->view('emails.ride_request_invoice')->with([
            'website_logo_url' => $this->setting->websiteLogoUrl(),
            'website_name' => $this->setting->get('website_name'),
            'website_address' => $this->setting->get('website_address'),
            'website_contact_number' => $this->setting->get('website_contact_number'),
            'website_contact_email' => $this->setting->get('website_contact_email'),
            'currency_symbol' => $this->setting->get('currency_symbol'),
            'user' => $this->user,
            'driver' => $this->driver,
            'invoie' => $this->invoice,
            'rideRequest' => $this->rideRequest
        ]);
    }
}
