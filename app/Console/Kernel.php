<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \App\Console\Commands\CreateAdmin::class,
        \App\Console\Commands\ChangeAdminPassword::class,
        \App\Console\Commands\AlertTripBookings::class,
        \App\Console\Commands\DriverServerWakeup::class,
        \App\Console\Commands\DeletePendingDriverBookings::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('trip-bookings:alert')->everyFiveMinutes();
        $schedule->command('driverservice:wakeup')->everyMinute();
        $schedule->command('DriverBookingBroadcast:Start')->everyMinute();
        $schedule->command('delete:pendingdriverbookings')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
