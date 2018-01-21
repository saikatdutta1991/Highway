<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RideRequest as Ride;
use App\Models\RideRequestInvoice;
use App\Models\Driver;
use Validator;
use App\Models\Setting;
use App\Models\User;


class RideRequest extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(RideRequestInvoice $rideRequestInvoice, Ride $rideRequest, Setting $setting, Api $api, Driver $driver, User $user)
    {
        $this->rideRequestInvoice = $rideRequestInvoice;
        $this->rideRequest = $rideRequest;
        $this->setting = $setting;
        $this->api = $api;
        $this->driver = $driver;
        $this->user = $user;
    }



    /**
     * shows intracity ride requests
     */
    public function showIntracityRideRequests(Request $request)
    {

        $rides = $this->rideRequest->with('user', 'driver', 'invoice')
        ->whereNotIn('ride_status', [Ride::INITIATED]);

        //search for only particular user rides
        if($request->user_id != '') {
            $rides = $rides->where('user_id', $request->user_id);
        }

        //search for only particular driver rides
        if($request->driver_id != '') {
            $rides = $rides->where('driver_id', $request->driver_id);
        }

        $rides = $rides->orderBy('created_at', 'desc')->paginate(100);

        $totalRides = $this->rideRequest->count();
        $completedRides = $this->rideRequest->where('ride_status', Ride::COMPLETED)->count();
        $canceledRides = $this->rideRequest->whereIn('ride_status', [Ride::USER_CANCELED, Ride::DRIVER_CANCELED])->count();        
        $ongointRides = $this->rideRequest->whereNotIn('ride_status', $this->rideRequest->notOngoigRideRequestStatusListDriver())->count();
        $cashRides = $this->rideRequest->where('payment_mode', Ride::CASH)->count();
        $onlineRides = $this->rideRequest->where('payment_mode', Ride::ONLINE)->count();
        
        return view('admin.intracity_rides', compact(
            'rides', 'totalRides', 'completedRides', 'canceledRides', 'ongointRides', 'cashRides', 'onlineRides'
        ));

    }




    /**
     * view in detail of a parcular intercity ride requests by id
     */
    public function showIntracityRideRequestDetails(Request $request)
    {
        $ride = $this->rideRequest->where('id', $request->ride_request_id)->with(
            'user', 'driver', 'invoice'
        )->first();
            
        $mapUrl = app('UtillRepo')->getGoogleStaicMapImageConnectedPointsUrl([
            [$ride->source_latitude, $ride->source_longitude],
            [$ride->destination_latitude, $ride->destination_longitude]
        ], [500, 500]);

        return view('admin.intracity_ride_details', compact('ride', 'mapUrl'));

    }





}
