<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{

    //constst device tokens device types list
    const ANDROID = 'ANDROID';
    const IOS = 'IOS';
    const WEB_CHROME = 'WEB_CHROME';
    const DEVICE_TYPES = ['ANDROID', 'IOS', 'WEB_CHROME'];


    protected $table = 'push_notification_device_tokens';

    public function getTableName()
    {
        return $this->table;
    }

}