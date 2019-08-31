<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Models\DriverBookingBroadcast;
use App\Models\RideRequestInvoice as Invoice;
use App\Models\Setting;

class DriverBooking extends Model
{

    protected $table = 'driver_bookings';
    
    const NOT_PAID = 'NOT_PAID';
    const PAID = 'PAID';
    const RATINGS = [1, 2, 3, 4, 5];

    protected $appends = ["status_text", "car_transmission_type", "driver_track_url", "trip_type", "pickup_location_map"];
    
    //status can be 
    //pending, 
    //waiting_for_drivers_to_accept, 
    //driver_assigned, 
    //user_canceled, 
    //driver_canceled, 
    //driver_started
    //driver_reached
    //trip_started
    //trip_ended

    public static function table()
    {
        return "driver_bookings";
    }

    public function getPickupLocationMapAttribute()
    {
        $key = Setting::get('google_maps_api_key');
        return  "http://maps.google.com/maps/api/staticmap?center={$this->pickup_latitude},{$this->pickup_longitude}&size=400x400&zoom=15&maptype=roadmap&markers=icon:%20http://ijiya.com/images/marker-images/image.png|shadow:true|{$this->pickup_latitude},{$this->pickup_longitude}&sensor=false&key={$key}";
    }


    public function getDriverTrackUrlAttribute()
    {
        return route("hiring.bookings.track", [ "booking_id" => $this->id ]);
    }

    public function getTripTypeAttribute()
    {
        return $this->is_outstation ? "Outstation" : "City";
    }


    public function getStatusTextAttribute()
    {
        if($this->status == 'pending') {
            return "Pending";
        } else if($this->status == 'waiting_for_drivers_to_accept') {
            return "Requesting";
        } else if($this->status == 'driver_assigned') {
            return "Driver assigned";
        } else if($this->status == 'driver_started') {
            return "Driver on the way";
        } else if($this->status == 'trip_started') {
            return "Ongoing";
        } else if($this->status == 'trip_ended' && $this->payment_status == "PAID") {
            return "Completed";
        } else if($this->status == 'trip_ended' && $this->payment_status == "NOT_PAID") {
            return "Payment pending";
        }
    }

    public function getCarTransmissionTypeAttribute()
    {
        return $this->car_transmission == "10" ? "Manual" : "Automatic";
    }

    
    /** relation with user */
    public function user()
    {
        return $this->belongsTo("App\Models\User", "user_id");
    }


    /** relation with driver */
    public function driver()
    {
        return $this->belongsTo("App\Models\Driver", "driver_id");
    }


    public function package()
    {
        return $this->belongsTo("App\Models\HirePackage", "package_id");
    }



    /** only date */
    public function onlyDate()
    {
        return Carbon::parse($this->datetime, 'UTC')->setTimezone('Asia/Kolkata')->format('d/m/Y');
    }


    /** only time */
    public function onlyTime()
    {
        return Carbon::parse($this->datetime, 'UTC')->setTimezone('Asia/Kolkata')->format('h:i A');
    }



    public function bookingDateTime()
    {
        return Carbon::parse($this->created_at, 'UTC')->setTimezone('Asia/Kolkata')->format('d.m.Y h:i A');
    }


    /**
     *  relatitionship with invoices
     */
    public function invoice()
    {
        return $this->belongsTo('App\Models\RideRequestInvoice', 'invoice_id');
    }


    public function formatedDate($timezone)
    {
        return Carbon::parse($this->datetime, 'UTC')->setTimezone($timezone)->format('d/m/Y');
    }

    public function formatedTime($timezone)
    {
        return Carbon::parse($this->datetime, 'UTC')->setTimezone($timezone)->format('h:i a');
    }

    public function formatedBookingDate($timezone)
    {
        return Carbon::parse($this->created_at, 'UTC')->setTimezone($timezone)->format('d/m/Y');
    }

    public function formatedBookingTime($timezone)
    {
        return Carbon::parse($this->created_at, 'UTC')->setTimezone($timezone)->format('h:i a');
    }




    /** get driver booking action with booking */
    public static function getDriverBookingAction($driverid)
    {

        /** check driver has any pending request */
        $booking = DriverBooking::join(DriverBookingBroadcast::table(), DriverBookingBroadcast::table().".booking_id", "=", DriverBooking::table().".id")
            ->where(DriverBookingBroadcast::table().".driver_id", $driverid)
            ->where(DriverBookingBroadcast::table().".status", "pending")
            ->select(DriverBooking::table().".*")
            ->first();

        if($booking) {
            return [$booking, "request_to_accept"];
        }


        /** check if driver has any onging booking */
        $booking = DriverBooking::with("package", "user", "invoice")->where("driver_id", $driverid)->whereIn("status", ["driver_started", "driver_reached", "trip_started"])->first();
        if($booking) {
            return [$booking, "ongoing_request"];
        }

        /** check if driver any booking has to give rating to user */
        $booking = DriverBooking::with("package", "user", "invoice")->where("driver_id", $driverid)->whereIn("status", ["trip_ended"])->where("user_rating", 0)->first();
        if($booking) {
            return [$booking, "rating"];
        }

        return [null, null];


    }



    /** 
     * calculate driver rating for ride requests
    */
    public static function getUserRideRatingDetails($userId)
    {  
        $selects = [
            'SUM('.self::table().'.user_rating) AS user_rating_sum',
            'COUNT('.self::table().'.id) AS ride_request_count'
        ];

        //find total number of requests and sum of total ratings
        $record = self::where('user_id', $userId)
            ->whereIn('user_rating', self::RATINGS)
            ->selectRaw(implode(',', $selects))->first();
        
        return [(integer)$record->user_rating_sum, $record->ride_request_count];

    }
    

    /** 
     * calculate driver rating for ride requests
    */
    public static function getDriverRideRatingDetails($driverId)
    {  
        $selects = [
            'SUM('.self::table().'.driver_rating) AS driver_rating_sum',
            'COUNT('.self::table().'.id) AS ride_request_count'
        ];

        //find total number of requests and sum of total ratings
        $record = self::where('driver_id', $driverId)
            ->whereIn('driver_rating', self::RATINGS)
            ->selectRaw(implode(',', $selects))->first();
        
        return [(integer)$record->driver_rating_sum, $record->ride_request_count];

    }



    /** get driver booking action with booking */
    public static function getDriverBookingActionForUser($userid)
    {

        /** check if driver has any payment pending booking */
        $booking = DriverBooking::with("package", "user", "invoice")->where("user_id", $userid)->whereIn("status", ["trip_ended"])->where("payment_status", "NOT_PAID")->first();
        if($booking) {
            return [$booking, "make_payment"];
        }

        /** check if driver any booking has to give rating to driver */
        $booking = DriverBooking::with("package", "user", "invoice")->where("user_id", $userid)->whereIn("status", ["trip_ended"])->where("driver_rating", 0)->first();
        if($booking) {
            return [$booking, "rating"];
        }

        return [null, null];


    }


    /** fetch bookings count */
    public static function getBookingsCount($userid)
    {
        $bookingsCount = new DriverBooking;
        if($userid) {
            $bookings = $bookingsCount->where("user_id", $userid);
        }
        return $bookingsCount->count();
    }

    /** fetch completed bookings count */
    public static function getCompletedBookingsCount($userid)
    {
        $bookingsCount = DriverBooking::where("status", "trip_ended")->where("payment_status", "PAID");
        if($userid) {
            $bookings = $bookingsCount->where("user_id", $userid);
        }
        return $bookingsCount->count();
    }

    /** fetch payment pending bookings count */
    public static function getPendingPaymentBookingsCount($userid)
    {
        $bookingsCount = DriverBooking::where("status", "trip_ended")->where("payment_status", "NO_PAID");
        if($userid) {
            $bookings = $bookingsCount->where("user_id", $userid);
        }
        return $bookingsCount->count();
    }
    

    /** get total earnings */
    public static function getTotalEarnings($userid)
    {
        $total = DriverBooking::join(Invoice::tablename(), Invoice::tablename().".id", "=", DriverBooking::table().".invoice_id");
        if($userid) {
            $total = $total->where(DriverBooking::table().".user_id", $userid);
        }

        return $total->sum(Invoice::tablename().".total");    
    }

    /** get total cash earnings */
    public static function getTotalCashEarnings($userid)
    {
        $total = DriverBooking::join(Invoice::tablename(), Invoice::tablename().".id", "=", DriverBooking::table().".invoice_id")
            ->where(DriverBooking::table().".payment_mode", "CASH");
        if($userid) {
            $total = $total->where(DriverBooking::table().".user_id", $userid);
        }

        return $total->sum(Invoice::tablename().".total");    
    }

    /** get total online earnings */
    public static function getTotalOnlineEarnings($userid)
    {
        $total = DriverBooking::join(Invoice::tablename(), Invoice::tablename().".id", "=", DriverBooking::table().".invoice_id")
            ->where(DriverBooking::table().".payment_mode", "ONLINE");
        if($userid) {
            $total = $total->where(DriverBooking::table().".user_id", $userid);
        }

        return $total->sum(Invoice::tablename().".total");    
    }

    

}