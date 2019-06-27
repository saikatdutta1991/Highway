<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use App\Models\DriverAccount;
use App\Models\DriverAccountTransaction;
use App\Models\DriverInvoice;
use App\Models\Setting;
use App\Repositories\Utill;

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
     * get driver account balance
     */
    public function getDriverAccount(Request $request)
    {
        $account = DriverAccount::createAndFetch($request->auth_driver->id);
        $transacitons = DriverAccountTransaction::where('account_id', $account->id)
            ->where('is_hidden_to_driver', false)
            ->orderBy('created_at', 'desc')
            ->select(['id', 'trans_id', 'amount', 'closing_amount', 'remarks', 'created_at'])
            ->paginate(10);

        $timezone = $request->auth_driver->timezone;
        $transacitons->map(function($item) use($timezone) {
            $item['date'] = $item->createdOn($timezone);
        });
        

        return $this->api->json(true, "ACCOUNT_INFORMATIONS", 'Account informations', [
            'currency_symbol' => Setting::get('currency_symbol'),
            'account_balance' => Utill::formatAmountDecimalTwoWithoutRound($account->balance),
            'transactions' => $transacitons->items(),
            'paging' => [
                'has_more' => $transacitons->hasMorePages(),
                'next_page_url' => $transacitons->nextPageUrl(),
            ]
        ]);
    }


    /**
     * get invoice payout details
     */
    public function getPayoutDetails(Request $request)
    {
        $invoices = [];

        $records = DriverInvoice::where('driver_id', $request->auth_driver->id)->orderBy('created_at', 'desc')->paginate(10);

        foreach($records as $record) {

            if($record->ride_type == 'city') {
                $from = $record->ride->source_address;
                $to = $record->ride->destination_address;
                $rideStatus = $record->ride->ride_status;
            } else {
                $from = $record->ride->from;
                $to = $record->ride->to;
                $rideStatus = $record->ride->status;
            }
            
            $invoices[] = [
                'ride_status' => $rideStatus,
                'from' => $from,
                'to' => $to,
                'ride_type' => $record->ride_type,
                'ride_cost' => $record->ride_cost,
                'tax' => $record->tax,
                'admin_commisssion' => $record->admin_commission,
                'driver_earnings' => $record->driver_earnings,
                'cancellation_charge' => $record->cancellation_charge,
                'date' => $record->createdOn($request->auth_driver->timezone)
            ];


        }

        return $this->api->json(true, 'PAYOUTS', 'Payout records fetched', [
            'currency_symbol' => Setting::get('currency_symbol'), 
            'records' => $invoices,
            'paging' => [
                'has_more' => $records->hasPages(),
                'next_page_url' => $records->nextPageUrl(),
            ]
        ]);

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
