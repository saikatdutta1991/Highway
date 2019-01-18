<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{

    protected $table = 'promotions';

    const BROADCAST_ALL = 'broadc_all';
    const BROADCAST_USERS = 'broadc_users';
    const BROADCAST_DRIVERS = 'broadc_drivers';

    const SCREATED = "created";
    const SPROCESSING = "processing";
    const SPROCESSED = "processed";


    /**
     * get table name statically
     */
    public static function table()
    {
        return 'promotions';
    }


    /**
     * get broadcast type text
     */
    public function broadcastTypeText()
    {
        switch ($this->broadcast_type) {
            case 'broadc_all':
                return 'Everyone';
                break;

            case 'broadc_users':
                return 'Users';
                break;

            case 'broadc_drivers':
                return 'Drivers';
                break;
        }
    }



}