<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\Driver;
use App\Models\RideRequest;
use App\Models\RideRequestInvoice;
use Carbon\Carbon;
use DB;
use App\Models\Trip\Trip;
use App\Models\Trip\TripBooking;
use App\Models\VehicleType;


class Payout extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Setting $setting, Api $api)
    {
        $this->setting = $setting;
        $this->api = $api;
    }



    /**
     * This function filters and shows drivers payouts records
     * for a specific duration of months(from date - to date)
     * admin can downooad the records as excel sheet
     */
    public function showFilteredPayouts(Request $request)
    {  
        /** varialbe to store records */
        $records = [];
        $totalRecords = 0;
        $cityRidesChecked = true;
        $highwayRidesChecked = false;
        $full_mobile_number = $request->full_mobile_number;

        if($request->ride_type == 'city_rides') {
            $cityRidesChecked = true;
            $highwayRidesChecked = false;
        } else if($request->ride_type == 'highway_rides') {
            $cityRidesChecked = false;
            $highwayRidesChecked = true;

        } else if($request->ride_type == 'both_city_highway_rides') {
            $cityRidesChecked = true;
            $highwayRidesChecked = true;
        }

        /** if from is submit then only */
        if($request->submit) {

            $fromDateConverted = Carbon::createFromFormat('d/m/Y H:i:s', $request->from_date.' 00:00:00', 'Asia/Kolkata')->setTimezone('UTC'); 
            $toDateConverted = Carbon::createFromFormat('d/m/Y H:i:s', $request->to_date.' 23:59:59', 'Asia/Kolkata')->setTimezone('UTC');

            /** fetch all drivers */
            $drivers = Driver::select('id')->get();

            /** loop through all drivers and fetch city and highway ride details */
            foreach($drivers as $driver) {

                /** fetch all city rides */
                $cityRides = [];
                if($cityRidesChecked) {
                    $cityRides = $this->getCityRides($driver->id, $fromDateConverted->toDateString(), $toDateConverted->toDateString(), $full_mobile_number)->toArray();
                    $totalRecords += count($cityRides);
                }

                /** fetch all highway rides for this current driver */
                $highwayRides = [];
                if($highwayRidesChecked) {
                    $highwayRides = $this->getHighwayRides($driver->id, $fromDateConverted->toDateString(), $toDateConverted->toDateString(), $full_mobile_number)->toArray();
                    $totalRecords += count($highwayRides);
                }

                $records[$driver->id] = ['city_rides' => $cityRides, 'highway_rides' => $highwayRides];
            }   

        }



        /** status collection to get text */
        $statusCollection = [
            'COMPLETED' => 'Completed',
            'TRIP_CANCELED_DRIVER' => 'Canceled',
            'DRIVER_CANCELED' => 'Canceled',
            'TRIP_ENDED' => 'Completed'
        ];

        /** get vehicle types */
        $vehicleTypes = VehicleType::allTypes();



        return view('admin.payouts.payout_filter', [
            'fromDate' => $request->from_date,
            'toDate' => $request->to_date,
            'cityRides' => $cityRidesChecked,
            'highwayRides' => $highwayRidesChecked,
            'records' => $records,
            'statusCollection' => $statusCollection,
            'totalRecords' => $totalRecords,
            'vehicleTypes' => $vehicleTypes,
            'full_mobile_number' => $full_mobile_number
        ]);
    }




    /**
     * get only highway ride by date range and driver id
     */
    protected function getHighwayRides($driverid, $fromDate, $toDate, $full_mobile_number)
    {
        $highwayRides = Trip::where(Trip::tablename().".driver_id", $driverid)
            ->whereIn(Trip::tablename().".status", [Trip::COMPLETED, Trip::TRIP_CANCELED_DRIVER])
            ->whereBetween(Trip::tablename().".trip_datetime", [$fromDate, $toDate])
            ->join(Driver::tablename(), Driver::tablename().'.id', Trip::tablename().".driver_id")
            ->join(TripBooking::tablename(), Trip::tablename().".id", TripBooking::tablename().'.trip_id')    
            ->join(RideRequestInvoice::tablename(), function($join){
                $join->on(TripBooking::tablename().'.invoice_id', '=', RideRequestInvoice::tablename().".id")
                    ->whereIn(TripBooking::tablename().'.booking_status', [TripBooking::BOOKING_CONFIRMED, Trip::TRIP_CANCELED_DRIVER]);
            })
            ->groupBy(TripBooking::tablename().'.trip_id');

        if($full_mobile_number) {
            $highwayRides = $highwayRides->where(Driver::tablename().".full_mobile_number", $full_mobile_number);
        }
            
        $highwayRides = $highwayRides->select([
            DB::raw(Driver::tablename().".id as driver_id"),
            DB::raw(Driver::tablename().".fname"),
            DB::raw(Driver::tablename().".lname"),
            DB::raw(Driver::tablename().".full_mobile_number"),
            DB::raw(Driver::tablename().".vehicle_type"),
            DB::raw(Driver::tablename().".vehicle_number"),

            Trip::tablename().".id",
            DB::raw(Trip::tablename().".from as from_location"),
            DB::raw(Trip::tablename().".to as to_location"),
            DB::raw(Trip::tablename().".trip_datetime as date"),
            Trip::tablename().".status",
            DB::raw(Trip::tablename().".start_time as ride_start_time"),
            DB::raw(Trip::tablename().".end_time as ride_end_time"),
            DB::raw("'' as ride_cancel_remarks"),

            RideRequestInvoice::tablename().".payment_mode",
            RideRequestInvoice::tablename().".currency_type",
            DB::raw("SUM(".RideRequestInvoice::tablename().".ride_fare) as ride_fare"),
            DB::raw("SUM(".RideRequestInvoice::tablename().".access_fee) as access_fee"),
            DB::raw("SUM(".RideRequestInvoice::tablename().".tax) as tax"),
            DB::raw("SUM(".RideRequestInvoice::tablename().".total) as total"),
            DB::raw("SUM(".RideRequestInvoice::tablename().".referral_bonus_discount) as referral_bonus_discount"),
            DB::raw("SUM(".RideRequestInvoice::tablename().".cancellation_charge) as cancellation_charge"),
            DB::raw("SUM(".RideRequestInvoice::tablename().".coupon_discount) as coupon_discount")
        ])
        ->get();
        
        return $highwayRides;

    }




    /**
     * get only city ride by date range and driver id
     */
    protected function getCityRides($driverid, $fromDate, $toDate, $full_mobile_number)
    {
        $cityRides = RideRequest::where(RideRequest::tablename().".driver_id", $driverid)
            ->whereIn(RideRequest::tablename().".ride_status", [RideRequest::COMPLETED, RideRequest::TRIP_ENDED, RideRequest::DRIVER_CANCELED])
            ->whereBetween(RideRequest::tablename().".created_at", [$fromDate, $toDate])
            ->leftJoin(RideRequestInvoice::tablename(), RideRequest::tablename().".ride_invoice_id", RideRequestInvoice::tablename().'.id')
            ->join(Driver::tablename(), Driver::tablename().'.id', RideRequest::tablename().".driver_id");

        if($full_mobile_number) {
            $cityRides = $cityRides->where(Driver::tablename().".full_mobile_number", $full_mobile_number);
        }

        $cityRides = $cityRides->select([
            DB::raw(Driver::tablename().".id as driver_id"),
            DB::raw(Driver::tablename().".fname"),
            DB::raw(Driver::tablename().".lname"),
            DB::raw(Driver::tablename().".full_mobile_number"),
            DB::raw(Driver::tablename().".vehicle_type"),
            DB::raw(Driver::tablename().".vehicle_number"),

            RideRequest::tablename().".id",
            DB::raw(RideRequest::tablename().".source_address as from_location"),
            DB::raw(RideRequest::tablename().".destination_address as to_location"),
            RideRequest::tablename().".ride_cancel_remarks",
            DB::raw(RideRequest::tablename().".ride_status as status"),
            RideRequest::tablename().".ride_start_time",
            RideRequest::tablename().".ride_end_time",
            DB::raw(RideRequest::tablename().".created_at as date"),

            RideRequestInvoice::tablename().".payment_mode",
            RideRequestInvoice::tablename().".currency_type",
            RideRequestInvoice::tablename().".ride_fare",
            RideRequestInvoice::tablename().".access_fee",
            RideRequestInvoice::tablename().".tax",
            RideRequestInvoice::tablename().".total",
            RideRequestInvoice::tablename().".referral_bonus_discount",
            RideRequestInvoice::tablename().".cancellation_charge",
            RideRequestInvoice::tablename().".coupon_discount"
        ])
        ->get();
        
        return $cityRides;

    }





}
