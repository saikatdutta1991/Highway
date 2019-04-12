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
use App\Models\VehicleType;

class RideRequestInvoice extends Mailable
{
    use Queueable, SerializesModels;

    public $rideRequest;
    public $vehicleType;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public function __construct(RideRequest $rideRequest)
    {
        $this->rideRequest = $rideRequest;
        $this->vehicleType = VehicleType::where('code', $rideRequest->ride_vehicle_type)->first();
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
        ->subject($this->setting->get('website_name').' ride invoice')
        ->view('emails.ride_request_invoice')->with([
            'user' => $this->rideRequest->user,
            'driver' => $this->rideRequest->driver,
            'invoice' => $this->rideRequest->invoice,
            'rideRequest' => $this->rideRequest,
            'service_name' => $this->vehicleType->name
        ]);
        
    }
}
