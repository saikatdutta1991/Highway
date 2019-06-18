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



}