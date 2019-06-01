<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Trip\Trip;
use App\Models\Trip\TripBooking;
use App\Models\Trip\TripPoint;
use App\Models\User;
use App\Models\Driver;
use App\Repositories\Otp;
use App\Repositories\Utill;
use App\Repositories\PushNotification;
use App\Models\DeviceToken;

class AlertTripBookings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'trip-bookings:alert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command sends notification via sms and push to users bookings prior to trip start';


    /**
     * Create a new command instance.
     *
     * @param  DripEmailer  $drip
     * @return void
     */
    public function __construct(Otp $smsProvider, PushNotification $pushHelper)
    {
        parent::__construct();
        
        $this->smsProvider = $smsProvider;
        $this->pushHelper = $pushHelper;
    }




    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Trip Bookings Alert Command Started.');
        $this->sendSmsAlert();
        $this->sendPushAlert();
        $this->info('Trip Bookings Alert Command Ended.');
    }


    /**
     * sending push alert
     */
    protected function sendPushAlert()
    {
        /** let take current time and add 2 hours to current time to create range */
        $minTime = Carbon::now();
        $maxTime = Carbon::now()->addHours(2);
        $this->line("minTime : {$minTime}");
        $this->line("maxTime : {$maxTime}");

        /** find bookings withing time range going to start which has not been notified yet */
        $bookings = $this->getBookings($minTime, $maxTime, 2);


        /** now, for each bookings send sms to user*/
        foreach($bookings as $booking) {

            $this->line("Processing sending alert for Booking id : {$booking->booking_id}");
            $this->line('Sending push notification to user');
            $pushBody = Utill::transMessage('app_messages.booking_alert_push', [
                'date' => Carbon::createFromFormat('Y-m-d H:i:s', $booking->trip_datetime)->setTimezone($booking->user_timezone)->format('d-m-Y'),
                'time' => Carbon::createFromFormat('Y-m-d H:i:s', $booking->trip_datetime)->setTimezone($booking->user_timezone)->format('h:i A')
            ]);
            $userDevicetokens = DeviceToken::where('entity_type', 'USER')->where('entity_id', $booking->user_id)->get()->pluck('device_token')->all();
            
            $pushRes = $this->pushHelper->setTitle('Booking Alert')
                ->setBody($pushBody)
                ->setIcon('logo')
                ->setClickAction('')
                ->setCustomPayload([
                    'booking_id' => $booking->booking_id, 
                    'type' => 'BOOKING_ALERT',
                    'tracklink' => $booking->trackBookingUrl(),
                    'boardingpointlink' => route('bookings.track.boarding-point-route', ['bookingid' => $booking->booking_id])
                ])
                ->setPriority(PushNotification::HIGH)
                ->setContentAvailable(true)
                ->setDeviceTokens($userDevicetokens, true)
                ->push();
            
            TripBooking::where('id', $booking->id)->update(['is_push_alert_sent' => true]);
        }
    }



    /**
     * send sms alert
     */
    protected function sendSmsAlert()
    {
        /** let take current time and add 4 hours to current time to create range */
        $minTime = Carbon::now();
        $maxTime = Carbon::now()->addHours(4);
        $this->line("minTime : {$minTime}");
        $this->line("maxTime : {$maxTime}");

        /** find bookings withing time range going to start which has not been notified yet */
        $bookings = $this->getBookings($minTime, $maxTime, 1);


        /** now, for each bookings send sms to user*/
        foreach($bookings as $booking) {

            $this->line("Processing sending alert for Booking id : {$booking->booking_id}");
            $this->line("Sending sms to : {$booking->user_mobile_number}");
            $message = Utill::transMessage('app_messages.booking_alert', [
                'date' => Carbon::createFromFormat('Y-m-d H:i:s', $booking->trip_datetime)->setTimezone($booking->user_timezone)->format('d-m-Y'),
                'time' => Carbon::createFromFormat('Y-m-d H:i:s', $booking->trip_datetime)->setTimezone($booking->user_timezone)->format('h:i A'),
                'tracklink' => $booking->trackBookingUrl(),
                'boardingpointlink' => route('bookings.track.boarding-point-route', ['bookingid' => $booking->booking_id])
            ]);
            $this->smsProvider->sendMessage($booking->user_country_code, $booking->user_mobile_number, $message);

            TripBooking::where('id', $booking->id)->update(['is_sms_alert_sent' => true]);
        }

    }





    /** 
     * returns bookings those are not informed yet prior to spefic hours 
     * with time range
     * alerttype : 1 for sms, 2 for push
     */
    protected function getBookings($minTime, $maxTime, $alerttype = 1)
    {
        $bookings = TripBooking::join(Trip::table(), function ($join) use($minTime, $maxTime, $alerttype) {
            
            $join->on(TripBooking::table().'.trip_id', '=', Trip::table().'.id')
                ->whereBetween(Trip::table().'.trip_datetime', [$minTime, $maxTime])
                ->where(TripBooking::table().'.booking_status', TripBooking::BOOKING_CONFIRMED);

            if($alerttype == 1) {
                $join->where(TripBooking::table().'.is_sms_alert_sent', '=', false);
            } else if($alerttype == 2) {
                $join->where(TripBooking::table().'.is_push_alert_sent', '=', false);
            }

        })
        ->join(User::table(), User::table().'.id', '=', TripBooking::table().'.user_id')
        ->join(Driver::table(), Driver::table().'.id', '=', Trip::table().'.driver_id')
        ->select([
            TripBooking::table().'.*',
            Trip::table().'.trip_datetime',
            User::table().'.country_code as user_country_code',
            User::table().'.mobile_number as user_mobile_number',
            User::table().'.full_mobile_number as user_full_mobile_number',
            User::table().'.timezone as user_timezone',
        ])
        ->get();

        return $bookings;
    }



}
