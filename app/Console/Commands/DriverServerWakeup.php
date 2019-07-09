<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Driver;
use App\Repositories\PushNotification;
use App\Models\Setting;

class DriverServerWakeup extends Command
{
    protected $signature = 'driverservice:wakeup';
    protected $description = 'This command sends push notification to driver, if availabled and not connected to socket';

    public function __construct(PushNotification $firebase)
    {
        parent::__construct();
        $this->firebase = $firebase;
    }

  
    public function handle()
    {
        $this->info('DriverServerWakeup@handle --> started');

        /** fetch driver push device tokens, those are availabe but not connected to sockets */
        $drivers = Driver::select(['id', 'email'])
        ->where('is_approved', true)
        ->where('is_available', true)
        ->where('is_connected_to_socket', false)
        ->with('deviceTokens:device_token,entity_id')
        ->get();

        /** get only tokens */
        $deviceTokens = $drivers->pluck('deviceTokens.*.device_token')->flatten()->toArray();

        /** send push message */
        $appname = Setting::get('website_name');
        $this->firebase
            ->setTitle("{$appname} Service Disconnected")
            ->setBody("Stay connected to byroad service to receive incoming rides or go offline by pressing offline button.")
            ->setCustomPayload([ 'wake_service' => true ])
            ->setPriority(PushNotification::HIGH)
            ->setDeviceTokens($deviceTokens)
            ->push();


        $this->info('DriverServerWakeup@handle --> ended');    

    }
}
