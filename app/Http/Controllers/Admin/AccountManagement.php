<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DriverAccount;
use App\Models\DriverAccountTransaction;
use App\Models\Driver;
use DB;

class AccountManagement extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Setting $setting, Api $api)
    {
        $this->setting = $setting;
        $this->api = $api;
    }



    /**
     * recharge driver account 
     * sends sms to driver
     */
    public function processAccountRecharge(Request $request)
    { 
        $driver = Driver::find($request->driver_id);

        /** if action type debit add - symbol to amount */
        $amount = $request->action_type == 'credit' ? $request->amount : "-{$request->amount}";
        list($account, $transaction) = DriverAccount::updateBalance($driver->id, $request->transaction_id, $amount, $request->remarks);

        /** clear transactions from driver */
        if($request->clear_previous == 'on') {
            DriverAccountTransaction::hideTransactionsFromDriver($driver->id, [$transaction->id]);
        }

        /** send sms */
        $driver->sendSms($transaction->remarks);
        
        return $this->api->json(true, "SUCCESS", 'Account recharged successfully');
    }






    /**
     * show account recharge form
     */
    public function showAccountRecharge(Request $request)
    {
        $driver = Driver::find($request->driverid);
        $account = DriverAccount::createAndFetch($driver->id);
        $transactions = $account->transactions()->orderBy('created_at', 'desc')->get();
        
        return view('admin.drivers.account_recharge', compact('driver', 'account', 'transactions'));
    }






    /**
     * show all drivers accounts
     */
    public function showDriverAccounts()
    {

        $drivers = Driver::leftJoin(DriverAccount::table(), DriverAccount::table().".driver_id", Driver::table().".id")
            ->select([
                Driver::table().".id", 
                Driver::table().".fname", 
                Driver::table().".lname", 
                Driver::table().".email",
                Driver::table().".country_code", 
                Driver::table().".mobile_number",
                DB::Raw(" IFNULL( `".DriverAccount::table()."`.`balance` , 0.00 ) as balance")
            ])
            ->get();

        return view('admin.drivers.accounts', compact('drivers'));

    }


}
