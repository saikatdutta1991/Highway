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

        /** if pickuppoint driver not started means trip not started yet
         * so send the pickup and drop point location
         */
        if(!$pickupPoint->isDriverStarted() || $dropPoint->isDriverReached()) {
            return response()->json([
                'source' => [
                    'lat' => $pickupPoint->latitude,
                    'lat' => $pickupPoint->longitude,
                    'address' => $pickupPoint->address
                ],
                'destination' => [
                    'lat' => $dropPoint->latitude,
                    'lat' => $dropPoint->longitude,
                    'address' => $dropPoint->address
                ]
            ]);
        } 
        /** else if 
         * pickup point reached means trip started
         * so send the driver location and drop point location
         */
        else if($pickupPoint->isDriverReached()) {
            return response()->json([
                'source' => [
                    'lat' => $driver->latitude,
                    'lat' => $driver->longitude,
                    'address' => ''
                ],
                'destination' => [
                    'lat' => $dropPoint->latitude,
                    'lat' => $dropPoint->longitude,
                    'address' => $dropPoint->address
                ]
            ]);
        }

        /** else send driver and source point location */
        else {
            return response()->json([
                'source' => [
                    'lat' => $driver->latitude,
                    'lat' => $driver->longitude,
                    'address' => ''
                ],
                'destination' => [
                    'lat' => $pickupPoint->latitude,
                    'lat' => $pickupPoint->longitude,
                    'address' => $pickupPoint->address
                ]
            ]);
        }



    }





}
