<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\UserTrip;
use App\Models\RideRequestInvoice as Invoice;
use App\Models\Driver;
use App\Models\User;

class TripInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $userTrip;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public function __construct(UserTrip $userTrip)
    {
        $this->userTrip = $userTrip;
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
       
        /* $staticMapImage = $this->utillRepo->getBase64Image(app('UtillRepo')->getGoogleStaicMapImageConnectedPointsUrl([
            [$this->rideRequest->source_latitude,$this->rideRequest->source_longitude],
            [$this->rideRequest->destination_latitude,$this->rideRequest->destination_longitude]
        ]), false); */

        return $this->from(
            $this->setting->get('email_support_from_address'), 
            $this->setting->get('email_from_name')
        )
        ->subject($this->setting->get('website_name').' trip invoice')
        ->view('emails.user_trip_invoice')->with([
            'user' => $this->userTrip->user,
            'driver' => $this->userTrip->trip->driver,
            'invoice' => $this->userTrip->invoice,
            'userTrip' => $this->userTrip
        ]);
        
    }
}
