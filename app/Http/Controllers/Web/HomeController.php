<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Trip\TripBooking;
use App\Models\Content;
use App\Models\VehicleType;
use Browser;
use App\Models\Setting;
use App\Models\Trip\TripPoint;
use App\Models\RideFare;
use App\Repositories\Api;

class HomeController extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }



    /**
     * common configs
     */
    public function getCommonConfigs()
    {
        return $this->api->json(true, 'COMMON_CONFIGS', 'Common configs', [
            'website_name' => Setting::get('website_name'),
            'website_title' => Setting::get('website_title'),
            'currency_symbol' => Setting::get('currency_symbol'),
            'favicon_url' => Setting::websiteFavIconUrl()
        ]);
    }




    /**
     * api to get list to services with basic fare
     * returns json response
     */
    public function getEstimatePrice(Request $request)
    {
        $pricesList = [];

        /** get all services */
        $services = VehicleType::allTypes();

        $distance = $request->has('distance') ? ($request->distance / 1000) : 0;
        
        /** loop through all services, and fetch base fares */
        foreach($services as $service) {

            /** fetching vehicle service details by vehicle_type_id */
            $serviceFare = RideFare::getServiceFareById($service['id']);
            $fareData = $serviceFare->calculateCityRideFare( 0, $distance, 0, '');

            $pricesList[] = [
                'service_id' => $service['id'],
                'service_name' => $service['name'],
                'fare' => [
                    'ride_fare' => $fareData['ride_fare'],
                    'access_fee' => $fareData['access_fee'],
                    'taxes' => $fareData['taxes'],
                    'total' => number_format($fareData['total'], 2, '.', '')
                ]
            ];
           
        }

        return $this->api->json(true, 'FARE_DATA', 'Fare data fetched successfully', $pricesList);
        
    }



    /**
     * show price estimate page
     * user can enter source and destination to get price estimate
     */
    public function showPriceEstimate()
    {
        return view('home.estimate_price');
    }



    /**
     * redirect and open app if user opens this link in mobile
     * or if browser then show some page
     */
    public function redirectReferral(Request $request)
    {

        if(Browser::isDesktop()) {
            return view('home.referral_share', ['referrer_code' => $request->referrer_code]);
        }

        $androidUserAppPackage = Setting::get('android_user_app_package');
        return redirect("https://play.google.com/store/apps/details?id={$androidUserAppPackage}&referrer={$request->referrer_code}");

    }






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
     * show google map and track booking boarding point route
     */
    public function trackBookingBoardingPointRoute(Request $request)
    {
        $booking = TripBooking::where('booking_id', $request->bookingid)->select(['id', 'boarding_point_id'])->first();
        $pickupPoint = TripPoint::where('id', $booking->boarding_point_id)->select(['id', 'label', 'address', 'tag', 'latitude', 'longitude'])->first();
        $mapUrl = "http://maps.google.com/maps?saddr=Your Location&daddr={$pickupPoint->latitude},{$pickupPoint->longitude}";
        return redirect($mapUrl);
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
        $servicename = VehicleType::where('code', $driver->vehicle_type)->first()->name;

        return view('tracking.track_booking', [
            'booking' => $booking,
            'user' => $user,
            'pickupPoint' => $pickupPoint,
            'dropPoint' => $dropPoint,
            'driver' => $driver,
            'trip' => $trip,
            'servicename' => $servicename
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
