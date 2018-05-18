<?php

namespace App\Models;

use App\Models\Events\UserCreated;
use App\Models\DeviceToken;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use App\Repositories\PushNotification;

class User extends Model
{


    use Notifiable;


    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => UserCreated::class
    ];


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


    /**
     * returns full name
     */
    public function fullname()
    {
        return $this->fname.' '.$this->lname;
    }




    public function reasonNotActivated()
    {
        return 'Admin has deactivated you account';
    }



    /**
     * relationship with ride_requests table
     */
    public function rideRequests()
    {
        return $this->hasMany('App\Models\RideRequest', 'user_id');
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



    /**
     * returns registerd on formater date(created_at)
     */
    public function registeredOn($timezone)
    {
        return \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->setTimezone($timezone)->format('d-m-Y h:i A');
    }



    /**
     * send sms
     */
    public function sendSms($message, &$error = null)
    {
       return app('App\Repositories\Otp')->sendMessage($this->country_code, $this->mobile_number, $message, $error);
    }



    /**
     * send password reset sms
     */
    public function sendPasswordResetSms($newPassword)
    {
        $websiteName = app('App\Models\Setting')->get('website_name');
        $smsText = <<<SMS_TEXT
        Your {$websiteName} password has been reset by admin. New password is : {$newPassword}
        \nPlease change your password after login.
SMS_TEXT;
        $this->sendSms($smsText);
    }


    /**
     * send password reset email
     */
    public function sendPasswordResetAdmin($newPassword)
    {
        return app('App\Repositories\Email')->sendUserPasswordResetAdmin($this, $newPassword);
    }



}
