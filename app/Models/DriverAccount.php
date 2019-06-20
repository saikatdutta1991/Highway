<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use Log;
use DB;
use App\Models\DriverAccountTransaction;

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
     * account balance cache key each driver
     */
    protected static function balanceCacheKey($driverid)
    {
        return "driver_account_{$driverid}_balance";
    }



    /** 
     * get driver balance only
     * returns balance from cache
     * or fetch from db store in cache
     */
    public static function getBalance($driverid) 
    {
        return Cache::rememberForever(DriverAccount::balanceCacheKey($driverid), function() use($driverid) {

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



    /** 
     * update account balance
     * if amount is negative(-) just add with current balance
     */
    public static function updateBalance($driverid, $transid, $amount, $remarks)
    {
        $account = DriverAccount::createAndFetch($driverid);
        $transaction = new DriverAccountTransaction;

        try {

            DB::beginTransaction();

            /** calculate account balance */
            $account->balance += $amount;
            $account->save();

            /** insert entry for transaction */
            $transaction->account_id = $account->id;
            $transaction->trans_id = $transid;
            $transaction->amount = $amount;
            $transaction->closing_amount = $account->balance;
            $transaction->remarks = $remarks;
            $transaction->save();
        
            DB::commit();

        } catch(\Exception $e) {
            DB::rollback();
            return [false, false];
        }


        /** store updated balance into cache */
        Cache::forever(DriverAccount::balanceCacheKey($driverid), $account->balance);
        
        return [$account, $transaction];
        
    }






}