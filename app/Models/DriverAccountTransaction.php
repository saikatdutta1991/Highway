<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DriverAccountTransaction extends Model
{

    protected $table = 'driver_account_transactions';
    
    public static function table()
    {
        return 'driver_account_transactions';
    }


    /**
     * relationship with driver account
     */
    public function account() 
    {
        return $this->belongsTo('App\Models\DriverAccount', 'account_id');
    }


    /**
     * hide transactions from driver all
     * except some input transaction ids
     */
    public static function hideTransactionsFromDriver($accountid, $exceptIds) 
    {
        DriverAccountTransaction::where('account_id', $accountid)
            ->whereNotIn('id', $exceptIds)
            ->update(['is_hidden_to_driver' => true]);
    }

    /**
     * returns registerd on formater date(created_at)
     */
    public function createdOn($timezone)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->setTimezone($timezone)->format('d M Y . h:m a');
    }



}