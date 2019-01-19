<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip\TripBooking;
use App\Models\Content;

class HomeController extends Controller
{


    /**
     * show privacy policy
     */
    public function showPrivacyPolicy()
    {
        $content = Content::where('name', 'privacy_policy')->first();
        $privacyPolicy = $content ? $content->content : '';
        return view('home.privacy_policy', compact('privacyPolicy'));
    }


    /**
     * show privacy policy
     */
    public function showTerms()
    {
        $content = Content::where('name', 'terms')->first();
        $terms = $content ? $content->content : '';
        return view('home.terms', compact('terms'));
    }



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




    /**
     * send json map location informations
     * this api will be as polling
     */
    public function trackBookingMap(Request $request)
    {
        $booking = TripBooking::where('booking_id', $request->bookingid)->first();
        $pickupPoint = $booking->boardingPoint;
        $dropPoint = $booking->destPoint;
        $driver = $booking->trip->driver;

        /**
         * check driver has not started trip or user trip completed
        */
        if( (!$pickupPoint->isDriverStarted() && !$pickupPoint->isDriverReached()) || $dropPoint->isDriverReached() ) {
            return response()->json([
                'source' => [
                    'lat' => $pickupPoint->latitude,
                    'lng' => $pickupPoint->longitude,
                    'address' => $pickupPoint->address,
                    'title' => 'Boarding Point'
                ],
                'destination' => [
                    'lat' => $dropPoint->latitude,
                    'lng' => $dropPoint->longitude,
                    'address' => $dropPoint->address,
                    'title' => 'Drop Point'
                ]
            ]);
        }

        /** check driver coming to pickup */
        if( $pickupPoint->isDriverStarted() && !$pickupPoint->isDriverReached() ) {
            return response()->json([
                'source' => [
                    'lat' => $driver->latitude,
                    'lng' => $driver->longitude,
                    'address' => '',
                    'title' => 'Driver On Way Pickup'
                ],
                'destination' => [
                    'lat' => $pickupPoint->latitude,
                    'lng' => $pickupPoint->longitude,
                    'address' => $pickupPoint->address,
                    'title' => 'Pickup Point'
                ]
            ]);
        }


        /** check driver has reached pickup point and will go or going to drop point */
        if($pickupPoint->isDriverReached() && !$dropPoint->isDriverReached()) {
            return response()->json([
                'source' => [
                    'lat' => $driver->latitude,
                    'lng' => $driver->longitude,
                    'address' => '',
                    'title' => 'Driver On Way Drop'
                ],
                'destination' => [
                    'lat' => $dropPoint->latitude,
                    'lng' => $dropPoint->longitude,
                    'address' => $dropPoint->address,
                    'title' => 'Drop Point'
                ]
            ]);
        }



    }





}
