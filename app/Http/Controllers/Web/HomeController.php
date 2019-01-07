<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip\TripBooking;


class HomeController extends Controller
{

    /**
     * show home landing page
     */
    public function showHomepage(Request $request)
    {
        return view('home.welcome');
    }





    /**
     * track booking page
     */
    public function trackBooking(Request $request)
    {
        $booking = TripBooking::where('booking_id', $request->bookingid)->first();
        $invoice = $booking->invoice;
        $user = $booking->user;
        $pickupPoint = $booking->boardingPoint;
        $dropPoint = $booking->destPoint;
        $driver = $booking->trip->driver;
        $trip = $booking->trip;
//dd($driver);

        return view('track_booking', [
            'booking' => $booking,
            'invoice' => $invoice,
            'user' => $user,
            'pickupPoint' => $pickupPoint,
            'dropPoint' => $dropPoint,
            'driver' => $driver,
            'trip' => $trip
        ]);
    }






}
