<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\DriverAccount;

class Dashboard extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }




    /**
     * get dashboard details
     */
    public function getDetails(Request $request)
    {   
        $cityRidesCount = Driver::getCityRidesCount($request->auth_driver->id);
        $canceledCityRidesCount = Driver::getCanceledCityRidesCount($request->auth_driver->id);
        $cityRidesCashCount = Driver::getCityRidesCashCount($request->auth_driver->id);
        $cityRidesOnlineCount = Driver::getCityRidesOnlineCount($request->auth_driver->id);

        $highwayTripsCount = Driver::getHighwayTripsCount($request->auth_driver->id);
        $canceledhighwayTripsCount = Driver::getCancelledHighwayTripsCount($request->auth_driver->id);
    
        $accountBalance = DriverAccount::getBalance($request->auth_driver->id);
        $earnings = Driver::getTotalEarnings($request->auth_driver->id);
        $cancellationCharges = Driver::getTotalCancellationCharges($request->auth_driver->id);


        return $this->api->json(true, "DASHBOARD", 'Dashboard details fetched', [
            'city_ride_count' => $cityRidesCount,
            "canceled_city_rides_count" => $canceledCityRidesCount,
            'city_ride_cash_count' => $cityRidesCashCount,
            'city_ride_online_count' => $cityRidesOnlineCount,
            'account_balance' => $accountBalance,
            'highway_trips_count' => $highwayTripsCount,
            'canceled_highway_trips_count' => $canceledhighwayTripsCount,
            'earnings' => $earnings,
            'cancellation_charges' => $cancellationCharges,
            'bank' => $request->auth_driver->bank
        ]);


    }


}
