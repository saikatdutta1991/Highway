<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Trip\TripBooking;
use App\Models\RideRequestInvoice as Invoice;
use App\Models\Driver;
use App\Models\User;

class TripInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public function __construct(TripBooking $booking)
    {
        $this->booking = $booking;
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
        ->subject($this->setting->get('website_name').' trip invoice')
        ->view('emails.user_trip_invoice')->with([
            'user' => $this->booking->user,
            'driver' => $this->booking->trip->driver,
            'invoice' => $this->booking->invoice,
            'userTrip' => $this->booking
        ]);
        
    }
}
