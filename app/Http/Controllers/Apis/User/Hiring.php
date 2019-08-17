<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HirePackage;
use Validator;
use App\Repositories\Utill;
use Carbon\Carbon;
use App\Models\DriverBooking;


class Hiring extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }


    /** 
     * returns all hiring pacakges
     */
    public function getHiringPackages()
    {
        $packages = HirePackage::orderBy('created_at', "desc")->get();
        return $this->api->json(true, "PACKAGES", "Packages fetched", [ "packages" => $packages ]);
    }


    /** create new hiring request */
    public function createRequest(Request $request)
    {
        $packageids = HirePackage::select('id')->get()->pluck('id')->toArray();
        list($latRegex, $longRegex) = Utill::regexLatLongValidate();

        $validator = Validator::make($request->all(), [
            "package_id" => "required|in:" . implode(",", $packageids),
            'pickup_address' => 'required|min:1|max:256', 
            'pickup_latitude' => ['required', 'regex:'.$latRegex], 
            'pickup_longitude' => ['required', 'regex:'.$longRegex], 
            'datetime' => ['required', 'date_format:Y-m-d H:i:s', function ($attribute, $value, $fail) {
                
                $datetime = Carbon::createFromFormat('Y-m-d H:i:s', $value, 'Asia/Kolkata')->setTimezone('UTC');
                $maxtime = Carbon::now()->addMinutes(15);
                if($datetime <= $maxtime) {
                    return $fail($attribute.' should be more than 15 minutes than current time');
                }
            }], 
            'payment_mode' => "required|in:CASH,ONLINE",
            "car_transmission" => "required|in:manual,automatic"
        ]);

        if($validator->fails()) {
            $messages = $validator->errors()->getMessages();
            $message = $messages[ key($messages) ][0];
            return $this->api->json(false, 'VALIDATION_ERROR', $message);
        }

        /** create new record */
        $booking = new DriverBooking;
        $booking->user_id = $request->auth_user->id;
        $booking->driver_id = 0;
        $booking->package_id = $request->package_id;
        $booking->pickup_address = $request->pickup_address;
        $booking->pickup_latitude = $request->pickup_latitude;
        $booking->pickup_longitude = $request->pickup_longitude;
        $booking->datetime = Carbon::createFromFormat('Y-m-d H:i:s', $request->datetime, $request->auth_user->timezone)->setTimezone('UTC');
        $booking->status = 'pending';
        $booking->payment_mode = $request->payment_mode;
        $booking->payment_status = DriverBooking::NOT_PAID;
        $booking->start_otp = rand(1000, 9999);
        $booking->car_transmission = $request->car_transmission == "manual" ? '10' : "01";
        $booking->save();

        return $this->api->json(true, "BOOKING_CREATED", "Your booking created successfully.");
    }



    /** get bookings */
    public function getBookings(Request $request)
    {
        $bookings = DriverBooking::where('user_id', $request->auth_user->id)
            ->with(["driver", "package", "invoice"])
            ->orderBy('datetime', 'desc')->get();
        return $this->api->json(true, "BOOKINGS", "Bookings fetched", [ "bookings" => $bookings ]);
    }




}
