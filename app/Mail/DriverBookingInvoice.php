<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\DriverBooking;
use App\Models\RideRequestInvoice as Invoice;
use App\Models\Driver;
use App\Models\User;

class DriverBookingInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public function __construct(DriverBooking $booking)
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
        ->subject($this->setting->get('website_name').' Part-time Driver Booking Invoice')
        ->view('emails.driver_booking_invoice')->with([
            'user' => $this->booking->user,
            'driver' => $this->booking->driver,
            'invoice' => $this->booking->invoice,
            'rideRequest' => $this->booking
        ]);
        
    }
}
