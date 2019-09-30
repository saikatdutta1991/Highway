<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DriverBooking;
use App\Models\DriverBookingBroadcast as Broadcast;
use App\Models\DeviceToken;
use App\Repositories\PushNotification;
use App\Repositories\SocketIOClient;
use App\Models\Driver;
use Carbon\Carbon;

class DriverBookingBroadcast extends Command
{

    protected $signature = 'DriverBookingBroadcast:Start';
    protected $description = 'This command looks for pending driver bookings and send broadcast to nearby drivers';

    public function __construct(Driver $driver, PushNotification $firebase, SocketIOClient $socketIOClient)
    {
        parent::__construct();
        $this->driver = $driver;
        $this->firebase = $firebase;
        $this->socketIOClient = $socketIOClient;
    }

    public function handle()
    {
        $this->info("DriverBookingBroadcast --> start");

        /** fetch all pending bookings */
        $bookings = DriverBooking::where('status', 'pending')->orderBy('datetime')->get(); //waiting_for_drivers_to_accept
        
        /** loop through all bookings */
        foreach($bookings as $booking) {

            /** fetch nearby drivers */
            $driverids = $this->getNearbyDriverIds($booking);
            $devicetokens = $this->getDevicetokens($driverids);
            
            /** send push notifications to drivers */
            $date = Carbon::parse($booking->datetime, 'UTC')->setTimezone('Asia/Kolkata')->format('d/m/Y');
            $time = Carbon::parse($booking->datetime, 'UTC')->setTimezone('Asia/Kolkata')->format('h:i A');
            $this->firebase->setTitle("Temp Driver request")->setBody("You got a new Temp Driver request on {$date} at {$time}. Please accept the request before anyone else does.")->setPriority(PushNotification::HIGH)->setDeviceTokens($devicetokens, false)->push();
            
            $this->socketIOClient->sendEvent([
                'to_ids' => implode(",", $driverids),
                'entity_type' => 'driver', //socket will make it uppercase
                'event_type' => 'new_driver_booking',
                'data' => [ "booking_id" => $booking->id ],
                'store_messsage' => true
            ]);

            /** insert record to db */
            $insertData = [];
            foreach($driverids as $id) {
                $insertData[] = [
                    "booking_id" => $booking->id,
                    "driver_id" => $id,
                    "status" => "pending"
                ];
            }
            Broadcast::insert($insertData);
            $booking->status = 'waiting_for_drivers_to_accept';
            $booking->save();

        }



        $this->info("DriverBookingBroadcast --> end");

    }


    /** fetch driver device tokens */
    protected function getDevicetokens($driverids) 
    {
        return DeviceToken::select(["device_token"])->whereIn("entity_id", $driverids)->where("entity_type", "DRIVER")->get()->pluck("device_token")->toArray();
    }



    /** nearby driver ids */
    protected function getNearbyDriverIds($booking)
    {
        $driverids = $this->driver->getNearbyDriversBuilder($booking->pickup_latitude, $booking->pickup_longitude, 10)
                ->where('is_approved', true)
                ->where('ready_to_get_hired', true);
            
        if($booking->car_transmission === '10') {
            $driverids = $driverids->where('manual_transmission', true);
        } else {
            $driverids = $driverids->where('automatic_transmission', true);
        }

        $driverids = $driverids->select('id')->get()->pluck('id')->toArray();
        return $driverids;
    }



}
