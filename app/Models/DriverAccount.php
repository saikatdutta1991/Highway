<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use Log;

class DriverAccount extends Model
{

    protected $table = 'driver_accounts';
    
    public static function table()
    {
        return 'driver_accounts';
    }


    /**
     * relationship with driver
     */
    public function driver()
    {
        return $this->belongsTo('App\Models\Driver', 'driver_id');
    }


    /** 
     * relationship with transactions
     */
    public function transactions()
    {
        return $this->hasMany('App\Models\DriverAccountTransaction', 'account_id');
    }



    /** 
     * get driver balance only
     * returns balance from cache
     * or fetch from db store in cache
     */
    public static function getBalance($driverid) 
    {
        return Cache::rememberForever("driver_account_{$driverid}_balance", function() use($driverid) {

            Log::info("DriverAccount::getBalance() -> Retriving driver account balance from db key : {$driverid}");

            $account = DriverAccount::createAndFetch($driverid);
            return $account->balance;

        });
    }



    /**
     * fetch driver account by driver id
     * if does not exists then create new record
     */
    public static function createAndFetch($driverid)
    {
        $account = DriverAccount::where('driver_id', $driverid)->first();
        
        if($account) {
            return $account;
        }

        $account = new DriverAccount;
        $account->balance = 0.00;
        $account->driver_id = $driverid;
        $account->save();

        return $account;

    }






}