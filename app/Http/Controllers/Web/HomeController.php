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

        return view('track_booking', [
            'booking' => $booking
        ]);
    }






}
