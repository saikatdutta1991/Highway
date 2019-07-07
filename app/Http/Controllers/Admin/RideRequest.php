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
use App\Repositories\SocketIOClient;


class RideRequest extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(SocketIOClient $socketIOClient, RideRequestInvoice $rideRequestInvoice, Ride $rideRequest, Setting $setting, Api $api, Driver $driver, User $user)
    {
        $this->rideRequestInvoice = $rideRequestInvoice;
        $this->rideRequest = $rideRequest;
        $this->setting = $setting;
        $this->api = $api;
        $this->driver = $driver;
        $this->user = $user;
        $this->socketIOClient = $socketIOClient;
        
    }



    /** complete ride request from admin panel */
    public function completeRide(Request $request)
    {
        /** find driver */
        $ride = $this->rideRequest->find($request->ride_request_id);


        /** create request object to call complete api */
        $request->request->add([ 
            'auth_driver' => $ride->driver,
            'ride_request_id' => $request->ride_request_id,
            'ride_distance' => $ride->ride_distance * 1000 // make km to meter, becaues this api takes distance in meter
        ]);

        $response = app('App\Http\Controllers\Apis\Driver\RideRequest')->endRideRequest($request);
        return $response;
    }


    /**
     * cancel ride request from admin panel
     * if ride is not complete or cancelled yet
     */
    public function cancelRide(Request $request)
    {

        $ride = $this->rideRequest->where('id', $request->ride_request_id)
            ->whereNotIn('ride_status', [Ride::USER_CANCELED, Ride::DRIVER_CANCELED, Ride::COMPLETED, Ride::INITIATED, Ride::TRIP_ENDED])
            ->first();

        try {

            $user = $ride->user;
            $driver = $ride->driver;

            $ride->ride_status = $request->on_behalf == 'user' ? Ride::USER_CANCELED : Ride::DRIVER_CANCELED;
            $ride->ride_cancel_remarks = $request->cancel_remarks;
            $ride->save();

            //chaning driver availability to 0
            $driver->is_available = 1;
            $driver->save();


            /** send push notificaiton and socket evnet to user */
            $notificationData = ['ride_request_id' => $ride->id, 'ride_status' => $ride->ride_status];
            $user->sendPushNotification("Ride Cancelled", "Your ride has been cancelled");
            $driver->sendPushNotification("Ride Cancelled", "Your ride has been cancelled");
            $this->socketIOClient->sendEvent([
                'to_ids' => $user->id,
                'entity_type' => 'user', //socket will make it uppercase
                'event_type' => 'ride_request_status_changed',
                'data' => $notificationData,
                'store_messsage' => true
            ]);
            $this->socketIOClient->sendEvent([
                'to_ids' => $driver->id,
                'entity_type' => 'driver', //socket will make it uppercase
                'event_type' => 'ride_request_status_changed',
                'data' => $notificationData,
                "store_messsage" => true
            ]);



        } catch(\Exception $e) {
            return $this->api->json(false, 'NOT_CANCELLED', 'Ride might be cancelled or completed or you might not allowed to cancel.');
        }
        

        return $this->api->json(true, 'CANCELLED', 'Request cancelled');

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
