<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HirePackage;
use App\Models\DriverBooking;
use App\Models\Driver;
use App\Models\DriverBookingBroadcast as Broadcast;
use Validator;
use App\Repositories\Utill;
use Carbon\Carbon;

class Hiring extends Controller
{
    public function __construct(Api $api)
    {
        $this->api = $api;
    }


    public function getBookingDetails(Request $request)
    {
        $booking = DriverBooking::with([ "driver", "user", "package", "invoice" ])->where("id", $request->booking_id)->first();
        $booking->driver_started = $booking->driver_started == "0000-00-00 00:00:00" ? "--" : Carbon::parse($booking->driver_started)->setTimezone("Asia/Kolkata")->format("d-m-Y @ h:i a");
        $booking->trip_started = $booking->trip_started == "0000-00-00 00:00:00" ? "--" : Carbon::parse($booking->trip_started)->setTimezone("Asia/Kolkata")->format("d-m-Y @ h:i a");
        $booking->trip_ended = $booking->trip_ended == "0000-00-00 00:00:00" ? "--" : Carbon::parse($booking->trip_ended)->setTimezone("Asia/Kolkata")->format("d-m-Y @ h:i a");
        
        return view("admin.hiring.booking_details_template", compact("booking"));
    }


    /** assignDriver */
    public function assignDriver(Request $request)
    {
        
        /** fetch driver and bookig from database */
        $booking = DriverBooking::find($request->booking_id);
        $driver = Driver::find($request->driver_id);

        if(!$booking || !$driver) {
            return $this->api->json(false, 'ASSIGN', 'Invalid booking or driver id');
        }

        /** add broadcast record */
        $broadcast = Broadcast::where("driver_id", $driver->id)->where("booking_id", $booking->id)->first() ?: new Broadcast;
        $broadcast->booking_id = $booking->id;
        $broadcast->driver_id = $driver->id;
        $broadcast->status = "pending";
        $broadcast->save();

        // update booking record
        $booking->driver_id = $driver->id;
        $booking->status = "driver_assigned";
        $booking->save();
        
        // update booking broadcaasts
        Broadcast::where("booking_id", $booking->id)->update(["status" => "rejected"]);
        Broadcast::where("booking_id", $booking->id)->where("driver_id", $driver->id)->update(["status" => "accepted"]);

        /** find user and send push notification */
        $date = Carbon::parse($booking->datetime, 'UTC')->setTimezone('Asia/Kolkata')->format('d/m/Y');
        $time = Carbon::parse($booking->datetime, 'UTC')->setTimezone('Asia/Kolkata')->format('h:i A');
        $booking->user->sendPushNotification("Driver Booking Confirmed", "Your Temp Driver request on {$date} at {$time} accepted by one driver.");
        $driver->sendPushNotification("Driver assigned", "You have been assigned for a driver request on {$date} at {$time} by admin. Check your driver bookings");
        return $this->api->json(true, 'ASSIGN', 'Driver assigned successfully');
    }


    /** show bookings */
    public function showUserBookings(Request $request)
    {
        /** fetch total bookings */
        $bookingsCount = DriverBooking::getBookingsCount($request->user_id);
        /** fetch completed bookings count */
        $completedCount = DriverBooking::getCompletedBookingsCount($request->user_id);
        /** fetch payment pending bookings count */
        $paymentPendingCount = DriverBooking::getPendingPaymentBookingsCount($request->user_id);
        /** get total earnings */
        $earnings = DriverBooking::getTotalEarnings($request->user_id);
        /** fetch total cash earnings */
        $cashEarnings = DriverBooking::getTotalCashEarnings($request->user_id);
        /** fetch total online earnings */
        $onlineEarnings = DriverBooking::getTotalOnlineEarnings($request->user_id);

        $bookings = DriverBooking::with([ "driver", "user", "package", "invoice" ])->orderBy("datetime", "desc");
        if($request->has('user_id')) {
            $bookings = $bookings->where("user_id", $request->user_id);
        }
        $bookings = $bookings->paginate(1000);

        return view("admin.hiring.bookings", [
            "bookings" => $bookings,
            "bookingsCount" => $bookingsCount,
            "completedCount" => $completedCount,
            "paymentPendingCount" => $paymentPendingCount,
            "earnings" => $earnings,
            "cashEarnings" => $cashEarnings,
            "onlineEarnings" => $onlineEarnings
        ]);
    }



    /** add package */
    public function addHiringPackage(Request $request)
    {
        $package = HirePackage::find($request->id) ?: new HirePackage;
        $package->name = $request->name;
        $package->hours = $request->hours;
        $package->charge = $request->charge;
        $package->per_hour_charge = $request->per_hour_charge;
        $package->night_charge = $request->night_charge;
        $package->grace_time = $request->grace_time;
        $package->night_hours = ($request->night_from  == '' || $request->night_to == '') ? "" : "{$request->night_from}-{$request->night_to}";
        $package->save();
        
        return redirect()->route("admin.hiring.package.add.show", [ 'id' => $package->id, 'success' => 1]);
    }



    /** show view where can package be added */
    public function showHiringPackageAdd(Request $request)
    {
        $package = HirePackage::find($request->id);
        $hours = Utill::getHoursList();
        return view("admin.hiring.add_package", [
            'hours' => $hours, "package" => $package
        ]);
    }




    /** show list of packages, where admin can add new packages */
    public function showHiringPackages()
    {
        $packages = HirePackage::orderBy('created_at', 'desc')->get();
        $hours = Utill::getHoursList();
        return view("admin.hiring.packages", [ "packages" => $packages, "hours" => $hours ]);
    }



}
