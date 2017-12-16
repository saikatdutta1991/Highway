<?php

namespace App\Models;

use App\Models\DeviceToken;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\PushNotification;

class User extends Model
{

    const ACTIVATED = 'ACTIVATED';

    protected $table = 'users';

    protected $hidden = ['password'];

    public function getTableName()
    {
        return $this->table;
    }


    public function fullMobileNumber()
    {
        return $this->full_mobile_number = $this->country_code.$this->mobile_number;
    }




    public function reasonNotActivated()
    {
        return 'Admin has deactivated you account';
    }




    /**
     * save push notification device token
     * call this method after user has been created or user id exists
     */
    public function addOrUpdateDeviceToken($deviceType, $token, $deviceId = '')
    {
        if(!in_array($deviceType, DeviceToken::DEVICE_TYPES)) {
            $errorCode = 'INVALID_DEVICE_TYPE';
            \Log::info('ADD_USER_DEVICE_TOKEN');
            \Log::info($errorCode.' Type: '.$deviceType);
            return $errorCode;
        }


        if($token == '') {
            $errorCode = 'DEVICE_TOKEN_EMPTY';
            \Log::info('ADD_USER_DEVICE_TOKEN');
            \Log::info($errorCode);
            return $errorCode;
        }

        $deviceTokenModel = app('App\Models\DeviceToken');

        $deviceToken = $deviceTokenModel->where('entity_type', 'USER')
        ->where('entity_id', $this->id)
        ->where('device_type', $deviceType)
        ->where('device_id', $deviceId);

        $deviceToken = $deviceToken->first() ?: new $deviceTokenModel;
        
        $deviceToken->entity_type = 'USER';
        $deviceToken->entity_id = $this->id;
        $deviceToken->device_type = $deviceType;
        $deviceToken->device_token = $token;
        $deviceToken->device_id = $deviceId;
        
        $deviceToken->save();

        return true;

    }





    /**
     * get all device tokens as array
     */
    public function getAllDeviceTokens()
    {

        $deviceTokenModel = app('App\Models\DeviceToken');

        $deviceTokens = $deviceTokenModel->where('entity_type', 'USER')
        ->where('entity_id', $this->id)->get();

        return $deviceTokens->pluck('device_token')->all();

    }





    /**
     * send push notification to user
     */
    public function sendPushNotification($title, $body, $custom = '', $clickAction = '')
    {
        $deviceTokens = $this->getAllDeviceTokens();

        $pushHelper = new PushNotification;
        $res = $pushHelper->setTitle($title)
        ->setBody($body)
        ->setIcon('logo')
        ->setClickAction('')
        ->setCustomPayload(['custom_data' => $custom])
        ->setPriority(PushNotification::HIGH)
        ->setContentAvailable(true)
        ->setDeviceTokens($deviceTokens)
        ->push();

        \Log::info('USER SEND PUSH NOTIFICATION');
        \Log::info('TOKENS : '.json_encode($deviceTokens));
        \Log::info('Response : '.$res);

        return true;

    }


    /**
     * set user timezone
     */
    public function saveTimezone($timezone = '', $save = false)
    {
        $this->timezone = app('UtillRepo')->getTimezone($timezone);
        if($save) {
            $this->save();
        }

        return $this->timezone;
    }



}
