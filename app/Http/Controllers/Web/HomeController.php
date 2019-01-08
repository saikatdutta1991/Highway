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
        $user = $booking->user;
        $pickupPoint = $booking->boardingPoint;
        $dropPoint = $booking->destPoint;
        $driver = $booking->trip->driver;
        $trip = $booking->trip;

        return view('tracking.track_booking', [
            'booking' => $booking,
            'user' => $user,
            'pickupPoint' => $pickupPoint,
            'dropPoint' => $dropPoint,
            'driver' => $driver,
            'trip' => $trip
        ]);
    }



    /**
     * send booking progress view
     */
    public function trackBookingProgress(Request $request)
    {
        $booking = TripBooking::where('booking_id', $request->bookingid)->first();
        $pickupPoint = $booking->boardingPoint;
        $dropPoint = $booking->destPoint;

        return view('tracking.booking_progress', [
            'booking' => $booking,
            'pickupPoint' => $pickupPoint,
            'dropPoint' => $dropPoint,
        ]);


    }






}
