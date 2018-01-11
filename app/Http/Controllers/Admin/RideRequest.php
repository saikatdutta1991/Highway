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

        $rides = $this->rideRequest->with('user', 'driver', 'invoice');



        $rides = $rides->paginate(100);


        $totalRides = $this->rideRequest->count();
        $completedRides = $this->rideRequest->where('ride_status', Ride::COMPLETED)->count();
        $canceledRides = $this->rideRequest->whereIn('ride_status', [Ride::USER_CANCELED, Ride::DRIVER_CANCELED])->count();        
        $ongointRides = $this->rideRequest->whereNotIn('ride_status', $this->rideRequest->notOngoigRideRequestStatusListDriver())->count();
        $cashRides = $this->rideRequest->where('payment_mode', Ride::CASH)->count();
        $payuRides = $this->rideRequest->where('payment_mode', Ride::PAYU)->count();
        
        
        /* try {


            //if search_by & keyword presend then only apply filter
            $search_by = $request->search_by;
            $skwd = $request->skwd;
            $location_name = $request->location_name;
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            //check location name available then search by location
            if($location_name != '' && $search_by == 'location' && $latitude != '' && $longitude != '') {
                $drivers = $this->driver->getNearbyDriversBuilder($latitude, $longitude, $request->radius);
            } else if($request->search_by != '' && $request->search_by != 'location' && $request->skwd != '') {
                $drivers = $drivers->where($request->search_by, 'like', '%'.$request->skwd.'%')->orWhere('lname', 'like', '%'.$request->skwd.'%');
            }


            //check if order_by is present
            $order_by = ($request->order_by == '' || $request->order_by == 'created_at') ? 'created_at' : $request->order_by;
            //if order(asc | desc) not present take desc default
            $order = ($request->order == '' || $request->order == 'desc') ? 'desc' : 'asc';
            $drivers = $drivers->orderBy($order_by, $order);


            $drivers = $drivers->paginate(100)->setPath('drivers');

        } catch(\Exception $e){
            //if any error happens take default
            $drivers = $this->driver->take(100)->paginate(2)->setPath('drivers');
        }
        
        $todaysDrivers = $this->driver->where('created_at', date('Y-m-d'))->count();
        $thisMonthDrivers = $this->driver->where('created_at', 'like', date('Y-m').'%')->count();
        $thisYearDrivers = $this->driver->where('created_at', 'like', date('Y').'%')->count();
        $totalApprovedDivers = $this->driver->where('is_approved', 1)->count();
 */

        return view('admin.intracity_rides', compact(
            'rides', 'totalRides', 'completedRides', 'canceledRides', 'ongointRides', 'cashRides', 'payuRides'
        ));

    }





}
