<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

use App\Models\RideRequest;
use App\Models\RideRequestInvoice;
use App\Models\Setting;
use App\Models\DriverInvoice;
use App\Models\DriverAccount;
use App\Repositories\Utill;
use App\Models\Driver;
use App\Models\Trip\Trip;
use App\Models\Trip\TripBooking;

class ProcessDriverInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ridetype; // city or highway
    protected $rideid;
    protected $driverid;

    public function __construct($ridetype, $rideid)
    {
        $this->ridetype = $ridetype;
        $this->rideid = $rideid;
    }


    public function handle()
    {
        if($this->ridetype == 'city') {
            $this->processCityRide();
        } else if ($this->ridetype == 'highway') {
            $this->processHighwayRide();
        }
    }


    /**
     * handle city ride driver invoice 
     */
    protected function processCityRide()
    {
        /** fetch ride request from db */
        $ride = RideRequest::where('id', $this->rideid)->select('id', 'ride_invoice_id', 'ride_status', 'driver_id')->first();

        $this->driverid = $ride->driver_id;
        
        /** check ride is completed, then process invoice or cancellation charge */
        if(in_array($ride->ride_status, [RideRequest::TRIP_ENDED, RideRequest::COMPLETED])) {
            $this->processCityRideInvoice($ride->ride_invoice_id);
        } elseif($ride->ride_status == RideRequest::DRIVER_CANCELED) {
            $this->processCityRideCancel();
        }

    }


    protected function processHighwayRide()
    {
        /** fetch trip from db */
        $trip = Trip::where('id', $this->rideid)->select('id', 'driver_id', 'status')->first();
        $this->driverid = $trip->driver_id;
        
        /** check trip completed, then proess invoice from each  booking*/
        if(in_array($trip->status, [Trip::TRIP_ENDED, Trip::COMPLETED])) {
            $this->processHighwayRideAdminCommission();
        } else if($trip->status == Trip::TRIP_CANCELED_DRIVER) {
            $this->processHighwayRideCancel();
        }
       

    }



    /**
     * takes all booking inovices and make, take admin commision from driver account
     */
    protected function processHighwayRideAdminCommission()
    {
        $bookings = TripBooking::where('trip_id', $this->rideid)
            ->where('payment_status', Trip::PAID)
            ->select('id', 'invoice_id')
            ->with(['invoice' => function($query) {
                $query->select('id', 'tax', 'total');
            }])
            ->get();

        $total = 0;
        $tax = 0;

        foreach($bookings as $booking) {
            $tax += $booking->invoice->tax;
            $total += $booking->invoice->total;
        }

        
        $adminCommissionPercentage = Setting::get('highway_ride_admin_commission') ?: 0;
        $adminCommission = ($total * $adminCommissionPercentage) / 100;
        $amtDedcAcc = $tax + $adminCommission;
        $driverEarnings = $total - $amtDedcAcc;
        
        $remarks = Utill::transMessage('app_messages.driver_account_ride_commission_deduct_remarks', [
            'appname' => Setting::get('website_name'),
            'csymbol' => Setting::get('currency_symbol'),
            'amount' => $amtDedcAcc,
            'ridetype' => 'Highway',
            'rideid' => $this->rideid
        ]);
        

        list($account, $transaction) = DriverAccount::updateBalance($this->driverid, Utill::randomChars(16), -$amtDedcAcc, $remarks);


        $driverInvoice = new DriverInvoice;
        $driverInvoice->driver_id = $this->driverid;
        $driverInvoice->ride_id = $this->rideid;
        $driverInvoice->ride_type = $this->ridetype;    
        $driverInvoice->ride_cost = $total;
        $driverInvoice->tax = $tax;
        $driverInvoice->admin_commission = $adminCommission;
        $driverInvoice->driver_earnings = $driverEarnings;
        $driverInvoice->save();


    }









    /**
     * duduct cancellation from driver account
     */
    public function processHighwayRideCancel()
    {
        $cancelcharge = Setting::get('driver_highway_ride_cancellation_charge') ?: 0;

        $remarks = Utill::transMessage('app_messages.driver_account_ride_cancellation_remarks', [
            'appname' => Setting::get('website_name'),
            'csymbol' => Setting::get('currency_symbol'),
            'amount' => $cancelcharge,
            'ridetype' => 'Highway',
            'rideid' => $this->rideid
        ]);
       
        list($account, $transaction) = DriverAccount::updateBalance($this->driverid, Utill::randomChars(16), -$cancelcharge, $remarks);

        $driverInvoice = new DriverInvoice;
        $driverInvoice->driver_id = $this->driverid;
        $driverInvoice->ride_id = $this->rideid;
        $driverInvoice->ride_type = $this->ridetype;    
        $driverInvoice->cancellation_charge = $cancelcharge;
        $driverInvoice->save();


        $driver = Driver::find($this->driverid);
        $driver->sendSms($remarks);
    }






    /**
     * deduct cancelaation charge from driver account, and city ride invoice amount
     */
    protected function processCityRideCancel()
    {
        $cancelcharge = Setting::get('driver_city_ride_cancellation_charge') ?: 0;

        $remarks = Utill::transMessage('app_messages.driver_account_ride_cancellation_remarks', [
            'appname' => Setting::get('website_name'),
            'csymbol' => Setting::get('currency_symbol'),
            'amount' => $cancelcharge,
            'ridetype' => 'City',
            'rideid' => $this->rideid
        ]);
       

        list($account, $transaction) = DriverAccount::updateBalance($this->driverid, Utill::randomChars(16), -$cancelcharge, $remarks);

        $driverInvoice = new DriverInvoice;
        $driverInvoice->driver_id = $this->driverid;
        $driverInvoice->ride_id = $this->rideid;
        $driverInvoice->ride_type = $this->ridetype;    
        $driverInvoice->cancellation_charge = $cancelcharge;
        $driverInvoice->save();

        

        $driver = Driver::find($this->driverid);
        $driver->sendSms($remarks);

    }





    /** 
     * process invoice, calculate admin commission and driver payouts
     */
    protected function processCityRideInvoice($invoiceid)
    {
        $invoice = RideRequestInvoice::where('id', $invoiceid)->select('id', 'tax', 'total')->first();
        
        $adminCommissionPercentage = Setting::get('city_ride_admin_commission') ?: 0;
        $adminCommission = ($invoice->total * $adminCommissionPercentage) / 100;
        $amtDedcAcc = $invoice->tax + $adminCommission;
        $driverEarnings = $invoice->total - $amtDedcAcc;
        
        $remarks = Utill::transMessage('app_messages.driver_account_ride_commission_deduct_remarks', [
            'appname' => Setting::get('website_name'),
            'csymbol' => Setting::get('currency_symbol'),
            'amount' => $amtDedcAcc,
            'ridetype' => 'City',
            'rideid' => $this->rideid
        ]);
        
        list($account, $transaction) = DriverAccount::updateBalance($this->driverid, Utill::randomChars(16), -$amtDedcAcc, $remarks);



        $driverInvoice = new DriverInvoice;
        $driverInvoice->driver_id = $this->driverid;
        $driverInvoice->ride_id = $this->rideid;
        $driverInvoice->ride_type = $this->ridetype;    
        $driverInvoice->ride_cost = $invoice->total;
        $driverInvoice->tax = $invoice->tax;
        $driverInvoice->admin_commission = $adminCommission;
        $driverInvoice->driver_earnings = $driverEarnings;
        $driverInvoice->save();


        
    }




}
