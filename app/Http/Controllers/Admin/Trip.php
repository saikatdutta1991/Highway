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
     * show user trip bookings
     */
    public function showBookings(Request $request)
    {
        $bookings = TripBooking::orderBy('created_at', 'desc')
            ->with(['trip', 'user', 'invoice']);

        if($request->has('trip_id')) {
            $bookings = $bookings->where("trip_id", $request->trip_id);
        }

        $bookings = $bookings->paginate(100);

        return view('admin.trips.show_bookings', compact('bookings'));
    
            
    }



    /**
     * show driver created trips 
     */
    public function showTrips(Request $request)
    {

        $trips = TripModel::orderBy('trip_datetime', 'desc')
            ->with(['driver', 'adminRoute'])
            ->withCount('bookings');
        
        if($request->has('trip_id')) {
            $trips = $trips->where("id", $request->trip_id);
        }


        $trips = $trips->paginate(100);

        return view('admin.trips.show_trips', compact('trips'));
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

        /** if admin creating new route then, check and stop from creating duplicate  */
        if($request->route_id == '' 
            && $this->route->where('from_location', $request->from_location)->where('to_location', $request->to_location)->exists()) {

            return $this->api->json(false, 'ROUTE_EXISTS', 'You are not supposed to create duplicate route.');

        }


        /** find ac and non-ac routes by from and to location id */
        $acRoute = $this->route->where('from_location', $request->from_location)
            ->where('to_location', $request->to_location)
            ->where('is_ac_enabled', true)
            ->first() ?: new $this->route;

        $nonAcRoute = $this->route->where('from_location', $request->from_location)
            ->where('to_location', $request->to_location)
            ->where('is_ac_enabled', false)
            ->first() ?: new $this->route;


        //if from and to location id same return error
        if($request->from_location == $request->to_location) {
            return $this->api->json(false, 'FROM_TO_LOCATION_SAME', 'Source and destination location can\'t be same.');
        }

        
        $acRoute->is_ac_enabled = true;
        $acRoute->from_location = $request->from_location;
        $acRoute->to_location = $request->to_location;
        $acRoute->base_fare = $request->base_fare;
        $acRoute->tax_fee = $request->tax_fee;
        $acRoute->access_fee = $request->access_fee;
        $acRoute->total_fare = $request->total_fare;
        $acRoute->status = AdminTripRoute::ENABLED;
        $acRoute->time = "{$request->aprox_time_hour}:{$request->aprox_time_min}:00";
        $acRoute->save();

        $nonAcRoute->is_ac_enabled = false;
        $nonAcRoute->from_location = $request->from_location;
        $nonAcRoute->to_location = $request->to_location;
        $nonAcRoute->base_fare = $request->base_fare_nonac;
        $nonAcRoute->tax_fee = $request->tax_fee_nonac;
        $nonAcRoute->access_fee = $request->access_fee_nonac;
        $nonAcRoute->total_fare = $request->total_fare_nonac;
        $nonAcRoute->status = AdminTripRoute::ENABLED;
        $nonAcRoute->time = "{$request->aprox_time_hour}:{$request->aprox_time_min}:00";
        $nonAcRoute->save();

        return $this->api->json(true, 'CREATED', 'Route created', [
            'acRoute' => $acRoute,
            'nonAcRoute' => $nonAcRoute
        ]);



    }



    /**
     * show all routes
     */
    public function showRoutes()
    {
        $routes = $this->route->orderBy('updated_at', 'desc')->where('is_ac_enabled', true)->paginate(1000);
        
        $routes->getCollection()->transform(function($acRoute){
            $acRoute['non_ac_route'] = $this->route->where('from_location', $acRoute->from_location)
                ->where('to_location', $acRoute->to_location)
                ->where('is_ac_enabled', false)
                ->first();
            return $acRoute;
        });

        return view('admin.trips.show_all_routes', compact('routes'));
    }


    /**
     * show edit route
     */
    public function showEditRoute(Request $request)
    {
        $route = $this->route->find($request->route_id);
        $acroute = $this->route->where('from_location', $route->from_location)
            ->where('to_location', $route->to_location)
            ->where('is_ac_enabled', true)->first();
        
        $nonacroute = $this->route->where('from_location', $route->from_location)
            ->where('to_location', $route->to_location)
            ->where('is_ac_enabled', false)->first();

        $locations = $this->location->orderBy('name')->get();
        return view('admin.trips.edit_route', compact('route', 'locations', 'nonacroute', 'acroute'));
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




    /** 
     * refund partial
     */
    public function partialRefundTripBooking(Request $request)
    {
        $booking = $this->booking->find($request->booking_id);
        $refundAmt = $request->refund_amount;
        $invoice = $booking->invoice;
        $transaction = $invoice->transaction;

        if($refundAmt > $invoice->total) {
            return $this->api->json(false, 'INVALID_REFUND_AMOUNT', 'Invalid refund amount');
        }


        try {

            DB::beginTransaction();

            $booking->payment_status = TripModel::PARTIAL_REFUNDED;
            $booking->save();

            $invoice->payment_status = TripModel::PARTIAL_REFUNDED;
            $invoice->cancellation_charge = $invoice->total - $refundAmt;
            $invoice->save();


            $razorpay = Gateway::instance('razorpay');
            $refund = $razorpay->refundPartial($transaction->trans_id, $refundAmt);


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
            $this->api->log('ADMIN_RAZORPAY_REFUND_PARTIAL_ERROR', $e);
            $this->api->log('ADMIN_RAZORPAY_REFUND_PARTIAL_ERROR', ['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
            return $this->api->json(false, 'UNKOWN_ERROR', 'Unknown error. Try again or contact to service provider');
        }


        return $this->api->json(true, 'REFUND_SUCCESS', 'Refund successfull');
    }


}
