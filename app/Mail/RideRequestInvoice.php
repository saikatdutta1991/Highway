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
        $this->utillRepo = app('UtillRepo');
       
        $staticMapImage = $this->utillRepo->getBase64Image(app('UtillRepo')->getGoogleStaicMapImageConnectedPointsUrl([
            [$this->rideRequest->source_latitude,$this->rideRequest->source_longitude],
            [$this->rideRequest->destination_latitude,$this->rideRequest->destination_longitude]
        ]), false);

        return $this->from(
            $this->setting->get('email_support_from_address'), 
            $this->setting->get('email_from_name')
        )
        ->subject('Welcome to ' . $this->setting->get('website_name'). ' :)')
        ->view('emails.ride_request_invoice')->with([
            'user' => $this->user,
            'driver' => $this->driver,
            'invoie' => $this->invoice,
            'rideRequest' => $this->rideRequest, 
            'satic_map_image' => $staticMapImage
        ]);
        
    }
}
