<?php

namespace App\Http\Controllers\Apis\Driver;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverBooking;
use App\Models\DriverBookingBroadcast;
use Carbon\Carbon;


class Hiring extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }

    /** get booking requests only that not accepted yet */
    public function getRequests(Request $request)
    {
        $requests = DriverBooking::where(DriverBooking::table() . ".status", "waiting_for_drivers_to_accept")
            ->join(DriverBookingBroadcast::table(), DriverBookingBroadcast::table() . ".booking_id", "=", DriverBooking::table() . ".id")
            ->where(DriverBookingBroadcast::table() . ".driver_id", $request->auth_driver->id)
            ->where(DriverBookingBroadcast::table() . ".status", "pending")
            ->select(DriverBooking::table() . ".*")
            ->orderBy(DriverBooking::table() . ".datetime", "asc")
            ->with("package");
        
        if($request->id) {
            $requests = $requests->where(DriverBooking::table() . ".id", $request->id);
            $requests = $requests->first();
            return $this->api->json(true, "REQUEST", "Request", [ "request" => $requests ]);
        } else {
            $requests = $requests->get();
            return $this->api->json(true, "REQUESTS", "Requests", [ "requests" => $requests ]);
        }
    
    }



    /** accept or reject incoming driver booking request */
    public function actionRequest(Request $request)
    {
        /** validate request id correct or not */
        $booking = DriverBooking::where(DriverBooking::table() . ".status", "waiting_for_drivers_to_accept")
            ->join(DriverBookingBroadcast::table(), DriverBookingBroadcast::table() . ".booking_id", "=", DriverBooking::table() . ".id")
            ->where(DriverBookingBroadcast::table() . ".driver_id", $request->auth_driver->id)
            ->where(DriverBookingBroadcast::table() . ".status", "pending")
            ->where(DriverBooking::table() . ".id", $request->request_id)
            ->select(DriverBooking::table() . ".*")
            ->with('user')
            ->first();
        
        if(!$booking) {
            return $this->api->json(true, "ERROR", "You are not allowed to accept or reject this request.");
        }


        if($request->action == "accept") {

            // update booking record
            $booking->driver_id = $request->auth_driver->id;
            $booking->status = "driver_assigned";
            $booking->save();
            
            // update booking broadcaasts
            DriverBookingBroadcast::where("booking_id", $request->request_id)->update(["status" => "rejected"]);
            DriverBookingBroadcast::where("booking_id", $request->request_id)->where("driver_id", $request->auth_driver->id)->update(["status" => "accepted"]);

            /** find user and send push notification */
            $date = Carbon::parse($booking->datetime, 'UTC')->setTimezone('Asia/Kolkata')->format('d/m/Y');
            $time = Carbon::parse($booking->datetime, 'UTC')->setTimezone('Asia/Kolkata')->format('h:i A');
            $booking->user->sendPushNotification("Driver Booking Confirmed", "Your Temp Driver request on {$date} at {$time} accepted by one driver.");

        } else if($request->action == "reject") {

            DriverBookingBroadcast::where("booking_id", $request->request_id)->where("driver_id", $request->auth_driver->id)->update(["status" => "rejected"]);
            
        }

        return $this->api->json(true, "ACTION_DONE", "Action taken succesfully");
    }




    /** get bookings */
    public function getBookings(Request $request)
    {
        $bookings = DriverBooking::whereNotIn("status", ["pending", "waiting_for_drivers_to_accept"])
            ->where("driver_id", $request->auth_driver->id)
            ->with("user", "package")
            ->get();

        return $this->api->json(true, "BOOKINGS", "Bookings", [ "bookings" => $bookings ]);
    }





}
