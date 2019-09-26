<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DriverBooking;
use App\Models\DriverBookingBroadcast as Broadcast;
use Carbon\Carbon;

class DeletePendingDriverBookings extends Command
{
  
    protected $signature = 'delete:pendingdriverbookings';
    protected $description = 'This will delete all pending bookings those are not assigned for 2 days';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("DeletePendingDriverBookings --> started");

        $pastdate = Carbon::now()->subDays(2);
        
        /** delete from broadcast table */
        Broadcast::join(DriverBooking::table(), DriverBooking::table().".id", "=", Broadcast::table().".booking_id")
            ->whereIn(DriverBooking::table().".status", ["waiting_for_drivers_to_accept", "pending"])
            ->where(DriverBooking::table().".updated_at", "<", $pastdate)
            ->forceDelete();

        /** remove from bookings table */
        DriverBooking::whereIn("status", ["waiting_for_drivers_to_accept", "pending"])
            ->where("updated_at", "<", $pastdate)
            ->forceDelete();


        $this->info("DeletePendingDriverBookings --> eneded");
    }
}
