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
use App\Models\VehicleType;


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
       
        $serviceTypes = VehicleType::allTypes();

        $rides = $this->rideRequest->with('user', 'driver', 'invoice')->whereNotIn('ride_status', [Ride::INITIATED]);
        $totalRides = $this->rideRequest;
        $completedRides = $this->rideRequest->where('ride_status', Ride::COMPLETED);
        $canceledRides = $this->rideRequest->whereIn('ride_status', [Ride::USER_CANCELED, Ride::DRIVER_CANCELED]);
        $ongointRides = $this->rideRequest->whereNotIn('ride_status', $this->rideRequest->notOngoigRideRequestStatusListDriver());
        $cashRides = $this->rideRequest->where('payment_mode', Ride::CASH)->where('ride_status', Ride::COMPLETED);
        $onlineRides = $this->rideRequest->where('payment_mode', Ride::ONLINE)->where('ride_status', Ride::COMPLETED);

        //search for only particular user rides
        if($request->user_id != '') {
            $rides = $rides->where('user_id', $request->user_id);
            $totalRides = $totalRides->where('user_id', $request->user_id);
            $completedRides = $completedRides->where('user_id', $request->user_id);
            $canceledRides = $canceledRides->where('user_id', $request->user_id);
            $ongointRides = $ongointRides->where('user_id', $request->user_id);
            $cashRides = $cashRides->where('user_id', $request->user_id);
            $onlineRides = $onlineRides->where('user_id', $request->user_id);
        }

        //search for only particular driver rides
        if($request->driver_id != '') {
            $rides = $rides->where('driver_id', $request->driver_id);
            $totalRides = $totalRides->where('driver_id', $request->driver_id);
            $completedRides = $completedRides->where('driver_id', $request->driver_id);
            $canceledRides = $canceledRides->where('driver_id', $request->driver_id);
            $ongointRides = $ongointRides->where('driver_id', $request->driver_id);
            $cashRides = $cashRides->where('driver_id', $request->driver_id);
            $onlineRides = $onlineRides->where('driver_id', $request->driver_id);
        }


        $rides = $rides->orderBy('created_at', 'desc')->paginate(1000);
        $totalRides = $totalRides->count();
        $completedRides = $completedRides->count();
        $canceledRides = $canceledRides->count();
        $ongointRides = $ongointRides->count();
        $cashRides = $cashRides->count();
        $onlineRides = $onlineRides->count();

        
        
        return view('admin.intracity_rides', compact(
            'rides', 'totalRides', 'completedRides', 'canceledRides', 'ongointRides', 'cashRides', 'onlineRides', 'serviceTypes'
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
