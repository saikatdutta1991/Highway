<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\Models\Setting as Set;
use App\Models\Trip\AdminTripLocation;
use App\Models\Trip\AdminTripLocationPoint;
use App\Models\Trip\AdminTripRoute;
use App\Repositories\Gateway;
use App\Models\Trip\TripBooking;
use App\Models\Trip\Trip as TripModel;
use App\Models\Transaction;
use Validator;


class Trip extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(
        Set $setting, 
        Api $api, 
        AdminTripLocation $location, 
        AdminTripLocationPoint $point,
        AdminTripRoute $route,
        TripBooking $booking,
        Transaction $transaction
    )
    {
        $this->setting = $setting;
        $this->api = $api;
        $this->location = $location;
        $this->point = $point;
        $this->route = $route;
        $this->booking = $booking;
        $this->transaction = $transaction;
    }




    

    /**
     * show admin route locations list
     */
    public function showLocations()
    {
        $locations = $this->location->orderBy('updated_at', 'desc')->get();
        return view('admin.trips.show_trip_locations', compact('locations'));
    }



    /**
     * create new admin trip location
     */
    public function createLocation(Request $request)
    {
        if($this->location->where('name', $request->name)->exists()) {
            return $this->api->json(false, 'LOCATION_EXISTS', 'Location exists with same name');
        }

        if($request->name == '') {
            return $this->api->json(false, 'LOCATION_NAME_EMPTY', 'Enter location name');
        }


        $location = new $this->location;
        $location->name = ucfirst($request->name);
        $location->save();

        return $this->api->json(true, 'LOCATION_CREATED', 'Location created', [
            'location' => $location
        ]);

    }




    /**
     * update new admin trip location
     */
    public function updateLocation(Request $request)
    {
        if($this->location->where('name', $request->name)->where('id', '<>', $request->id)->exists()) {
            return $this->api->json(false, 'LOCATION_EXISTS', 'Location exists with same name');
        }

        if($request->name == '') {
            return $this->api->json(false, 'LOCATION_NAME_EMPTY', 'Enter location name');
        }


        $location = $this->location->find($request->id);
        $location->name = ucfirst($request->name);
        $location->save();

        return $this->api->json(true, 'LOCATION_UPDATED', 'Location updated', [
            'location' => $location
        ]);

    }



    /**
     * show location and related points
     */
    public function showLocation(Request $request)
    {
        $location = $this->location->find($request->location_id);
        return view('admin.trips.show_trip_location_points', compact('location'));
    }



    /**
     * this will remove all points to specific location and create new
     */
    public function createLocationPoints(Request $request)
    {
        $location = $this->location->find($request->location_id); 

        //delete all points related to this location id
        $this->point->where('admin_trip_location_id', $location->id)->forceDelete();

        //create new points
        foreach($request->points as $point) {
            $p = new $this->point;
            $p->admin_trip_location_id = $location->id;
            $p->label = $point['label'];
            $p->address = $point['address'];
            $p->latitude = $point['latitude'];
            $p->longitude = $point['longitude'];
            $p->save();
        }

        return $this->api->json(true, 'LOCATION_POINTS_UPDATED', 'Location points updated');

    }



    /**
     * show page for add new route
     */
    public function showNewRoute()
    {
        $locations = $this->location->orderBy('name')->get();
        return view('admin.trips.add_new_route', compact('locations'));
    }


    /**
     * create new route or update route if route_id param exists
     */
    public function addNewRoute(Request $request)
    {
        //check any route exists with same source and destination
        $route = $this->route->where('from_location', $request->from_location)->where('to_location', $request->to_location);
        if($request->route_id != '') {
            $route = $route->where('id', '<>', $request->route_id);
        }
        $route = $route->first();

        if($route) {
            return $this->api->json(false, 'ROUTE_EXISTS', 'Same source and destination route already exists.');
        }

        //if route_id exists then fetch saved route and update
        if($request->route_id != '') {
            $route = $this->route->find($request->route_id);
        } else  {
            $route = new $this->route;
        }

        //if from and to location id same return error
        if($request->from_location == $request->to_location) {
            return $this->api->json(false, 'FROM_TO_LOCATION_SAME', 'Source and destination location can\'t be same.');
        }

        
        $route->from_location = $request->from_location;
        $route->to_location = $request->to_location;
        $route->base_fare = $request->base_fare;
        $route->tax_fee = $request->tax_fee;
        $route->access_fee = $request->access_fee;
        $route->total_fare = $request->total_fare;
        $route->status = AdminTripRoute::ENABLED;
        $route->save();

        return $this->api->json(true, 'CREATED', 'Route created', [
            'route' => $route
        ]);



    }



    /**
     * show all routes
     */
    public function showRoutes()
    {
        $routes = $this->route->orderBy('updated_at', 'desc')->paginate(100);
        return view('admin.trips.show_all_routes', compact('routes'));
    }


    /**
     * show edit route
     */
    public function showEditRoute(Request $request)
    {
        $route = $this->route->find($request->route_id);
        $locations = $this->location->orderBy('name')->get();
        return view('admin.trips.edit_route', compact('route', 'locations'));
    }



    /**
     * show all canceled trips
     */
    public function showCanceledBookings()
    {
        $cnclBookings = $this->booking->whereIn('booking_status', [TripModel::TRIP_CANCELED_DRIVER, TripBooking::BOOKING_CANCELED_USER])
        ->where('payment_mode', TripModel::ONLINE)
        ->orderBy('created_at')
        ->orderByRaw("FIELD(payment_status, 'PAID', 'FULL_REFUNDED') ASC")
        ->with('trip', 'invoice', 'user')
        ->paginate(100);
    
        return view('admin.trips.show_canceled_bookings', compact('cnclBookings'));
    }



    /**
     * full refund to userbookings
     */
    public function fullRefundTripBooking(Request $request)
    {
        $booking = $this->booking->find($request->booking_id);
        $invoice = $booking->invoice;
        $transaction = $invoice->transaction;

        try {

            DB::beginTransaction();

            $booking->payment_status = TripModel::FULL_REFUNDED;
            $booking->save();

            $invoice->payment_status = TripModel::FULL_REFUNDED;
            $invoice->save();


            $razorpay = Gateway::instance('razorpay');
            $refund = $razorpay->refundFull($transaction->trans_id);


            if(!$refund['success']) {
                return $this->api->json(false, $refund['error_code'], $refund['message']);
            }


            $t = new $this->transaction;
            $t->trans_parent_id = $transaction->id;
            $t->trans_id = $refund['refund_id'];
            $t->amount = -$refund['amount'];
            $t->currency_type = $refund['currency_type'];
            $t->gateway = $razorpay->gatewayName();   
            $t->extra_info = json_encode($refund['extra']);
            $t->status = $refund['status'];
            $t->save();


            DB::commit();

        } catch(\Exception $e) {dd($e);
            DB::rollback();
            $this->api->log('ADMIN_RAZORPAY_REFUND_FULL_ERROR', $e);
            $this->api->log('ADMIN_RAZORPAY_REFUND_FULL_ERROR', ['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return $this->api->json(false, 'UNKOWN_ERROR', 'Unknown error. Try again or contact to service provider');
        }


        return $this->api->json(true, 'REFUND_SUCCESS', 'Refund successfull');


    }



}
