<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use DB;
use App\Repositories\Email;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Repositories\SocketIOClient;
use App\Models\Trip as TripModel;
use App\Models\TripPoint;
use App\Repositories\Utill;
use Validator;

class Trip extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(TripPoint $tripPoint, TripModel $trip, Utill $utill, Setting $setting, Email $email, Api $api, SocketIOClient $socketIOClient)
    {
        $this->tripPoint = $tripPoint;
        $this->trip = $trip;
        $this->utill = $utill;
        $this->setting = $setting;
        $this->email = $email;
        $this->api = $api;
        $this->socketIOClient = $socketIOClient;
    }



    /**
     * search trips
     */
    public function searchTrips(Request $request)
    {

        $sRadius = $request->s_radius != '' ? $request->s_radius : 5;
        $dRadius = $request->d_radius != '' ? $request->d_radius : 5;

        //check source and destination pickup points are missing or not
        list($sMinLat, $sMaxLat, $sMinLng, $sMaxLng) = $this->utill->getRadiousLatitudeLongitude(
            $request->s_latitude, $request->s_longitude, $sRadius
        );
        list($dMinLat, $dMaxLat, $dMinLng, $dMaxLng) = $this->utill->getRadiousLatitudeLongitude(
            $request->d_latitude, $request->d_longitude, $dRadius
        );

        $tripTable = $this->trip->getTableName();
        $trips = $this->tripPoint->leftJoin($tripTable, "{$tripTable}.id", '=', "{$this->tripPoint->getTableName()}.trip_id");
        //matching nearby sources points
        if($request->s_latitude != '' && $request->s_longitude != '') {

            $trips = $trips->where(function($query) use($sMinLat, $sMaxLat, $sMinLng, $sMaxLng){
                $query->whereBetween("{$this->tripPoint->getTableName()}.source_latitude", [$sMinLat, $sMaxLat])
                ->whereBetween("{$this->tripPoint->getTableName()}.source_longitude", [$sMinLng, $sMaxLng]);
            });
        }
        //matching nearby destination points
        if($request->d_latitude != '' && $request->d_longitude != '') {
            
            $trips = $trips->where(function($query)use($dMinLat, $dMaxLat, $dMinLng, $dMaxLng){
                $query->whereBetween("{$this->tripPoint->getTableName()}.destination_latitude", [$dMinLat, $dMaxLat])
                ->whereBetween("{$this->tripPoint->getTableName()}.destination_longitude", [$dMinLng, $dMaxLng]);
            });
        }
        //matching trip not canceled or started or completed
        $trips = $trips->whereNotIn("{$this->tripPoint->getTableName()}.trip_status", [TripModel::COMPLETED, TripModel::TRIP_STARTED]);

        if($date = $this->trip->createDate($request->date)) {
            $trips = $trips->where("{$tripTable}.trip_date_time", 'like', $date->toDateString().'%');
        }

        $trips = $trips->select("{$this->tripPoint->getTableName()}.*")
        ->with('trip', 'trip.driver')
        ->get();

        return $this->api->json(true, 'TRIPS', 'Trips', [
            'count' => $trips->count(),
            'trips' => $trips
        ]);



    }



}
