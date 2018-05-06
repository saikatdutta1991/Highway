<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use Illuminate\Http\Request;
use App\Models\Setting as Set;
use App\Models\AdminRoute;
use App\Models\AdminRoutePoint;
use Validator;


class Trip extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(
        Set $setting, 
        Api $api, 
        AdminRoute $route, 
        AdminRoutePoint $routePoint
    )
    {
        $this->setting = $setting;
        $this->api = $api;
        $this->route = $route;
        $this->routePoint = $routePoint;
    }


    /**
     * show all admin routes
     */
    public function showRoutes(Request $request)
    {
        $routes = $this->route->with('points')->orderBy('created_at', 'desc')->paginate(1000);
        return view('admin.trips.show_admin_routes', compact('routes'));
    }


    /**
     * shows add new route page
     */
    public function showAddNewRoute(Request $request)
    {
        $setting = $this->setting;
        return view('admin.trips.add_new_route', compact('setting'));
    }


    /**
     * adds new trip route
     */
    public function addNewRoute(Request $request)
    {
        //validate trip create request       
        $validator = Validator::make(
            $request->all(), $this->route->createTripValidationRules($request)
        );

        //if validation fails
        if($validator->fails()) {
            
            $errors = [];
            foreach($validator->errors()->getMessages() as $fieldName => $msgArr) {
                $errors[$fieldName] = $msgArr[0];
            }
            return $this->api->json(false, 'VALIDATION_ERROR', 'Fill all the fields before create trip', [
                'errors' => $errors
            ]);
        }
        
        $route = new $this->route;
        $route->name = ucfirst($request->name);
        $route->status = AdminRoute::ENABLED;

        //initializing route point
        $points = [];

        try {

            DB::beginTransaction();
            
            //save route
            $route->save();
            
            /**initialize point order, source point index, destination point index */
            $pointOrder = 1;
            $sourcePointIndex = 0;
            $destinationPointIndex = count($request->points) - 1;

            /**loop all points and save */
            foreach($request->points as $index => $point) {
                
                $routePoint = new $this->routePoint;
                $routePoint->admin_route_id = $route->id;
                $routePoint->order = $pointOrder++;
                $routePoint->address = $point['address'];
                $routePoint->latitude = $point['latitude'];
                $routePoint->longitude = $point['longitude'];
                $routePoint->city = ucfirst($point['city']);
                $routePoint->country = ucfirst($point['country']);
                $routePoint->zip_code = $point['zip_code'];
                
                /** add tag for source and destination */
                if($index == $sourcePointIndex) {
                    $routePoint->tag = 'SOURCE';
                } else if($index == $destinationPointIndex) {
                    $routePoint->tag = 'DESTINATION';
                } else {
                    $routePoint->tag = 'INTERMEDIATE';
                }
                
                $routePoint->save(); 
                $points[] = $routePoint;

            }
        
            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('CREATE_TRIP_ERROR', $e->getMessage());
            return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }
        
        
        
        return $this->api->json(true, "TRIP_CREATED", 'Trip created', [
            'route' => $route,
            'route_points' => $points
        ]);
    }



    /**
     * delete route
     */
    public function deleteRoute(Request $request)
    {
        $route = $this->route->find($request->route_id);

        try {
            DB::beginTransaction();

            //delete route points
            $this->routePoint->where('admin_route_id', $route->id)->forceDelete();

            //delete route 
            $route->forceDelete();

            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            $this->api->log('DELETE_ROUTE_ERROR', $e->getMessage());
            return $this->api->unknownErrResponse(['error_text', $e->getMessage(), 'line' => $e->getLine(), 'file' => $e->getFile()]);
        }


        return $this->api->json(true, 'ROUTE_DELETED', 'Route deleted');

    }



    /**
     * show trip points
     */
    // public function showTripPoints(Request $request)
    // {
    //     $points = $this->tripPoint->orderBy('created_at', 'desc');

    //     /** specific city */
    //     if($request->city != "") {
    //         $points = $points->where('city', 'like', $request->city);
    //     }

    //     /** specific country */
    //     if($request->country != "") {
    //         $points = $points->where('country', 'like', $request->country);
    //     }

    //     $points = $points->paginate(100);
    //     return view('admin.trips.show_trip_points', compact('points'));
    // }



    /**
     * show add new trip point(only one single point for trips)
     */
    /* public function showAddPoint()
    {
        $setting = $this->setting;
        return view('admin.trips.add_new_point', compact('setting'));
    } */



    /**
     * add new trip point
     */
    /* public function addNewPoint(Request $request)
    {
        list($latRegex, $longRegex) = app('UtillRepo')->regexLatLongValidate();
        $validator = Validator::make($request->all(), [
            'address' => 'required|min:1|max:500', 
            'city' => 'required|min:1|max:100', 
            'country' => 'required|min:1|max:100', 
            'zip_code' => 'required|min:1|max:100', 
            'latitude' => ['required', 'regex:'.$latRegex], 
            'longitude' => ['required', 'regex:'.$longRegex],
        ]);

        //if validation fails
        if($validator->fails()) {
            
            $errors = [];
            foreach($validator->errors()->getMessages() as $fieldName => $msgArr) {
                $errors[$fieldName] = $msgArr[0];
            }
            return $this->api->json(false, 'VALIDATION_ERROR', 'Fill all the fields before create trip', [
                'errors' => $errors
            ]);
        }


        $tripPoint = new $this->tripPoint;
        $tripPoint->address = $request->address;
        $tripPoint->latitude = $request->latitude;
        $tripPoint->longitude = $request->longitude;
        $tripPoint->city = $request->city;
        $tripPoint->country = $request->country;
        $tripPoint->zip_code = $request->zip_code;
        $tripPoint->save();

        return $this->api->json(true, "TRIP_POINT_ADDED", 'Trip point added', [
            'trip_point' => $tripPoint
        ]);

    } */



    /**
     * delete trip point
     */
    /* public function deleteTripPoint(Request $request)
    {
        $point = $this->tripPoint->find($request->point_id);
        if($point) {
            $point->delete();
        }

        return $this->api->json(true, 'POINT_DELETED', 'Point deleted');
    } */




}
