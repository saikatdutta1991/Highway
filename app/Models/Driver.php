<?php

namespace App\Models;

use App\Models\DeviceToken;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\PushNotification;

class Driver extends Model
{

    const ACTIVATED = 'ACTIVATED';

    protected $table = 'drivers';
    protected $hidden = ['password', 'profile_photo_path'];

    public function getTableName()
    {
        return $this->table;
    }


    /**
     * return string full mobile number (country_code + mobile_number)
     */
    public function fullMobileNumber()
    {
        return $this->full_mobile_number = $this->country_code.$this->mobile_number;
    }



    /**
     * save profile photo from given photo url after download
     */
    public function downloadAndSavePhoto($url, $prefix, $save = false)
    {
        $utill = app('App\Repositories\Utill');

        try {

            $path = $this->profilePhotoSaveAbsPath();
            $extension = $utill->getImageExtensionFromUrl($url);
            $fileName = $this->generateProfilePhotoName($prefix, $extension);

            $utill->downloadFile($url, $path.'/'.$fileName);

        } catch(\Exception $e) {
            \Log::info('DRIVER FILE DOWNLAOD SAVE ERROR');
            \Log::info($e->getMessage());
            \Log::info($e->getFile());
            \Log::info($e->getLine());
        }
        
        $this->profile_photo_path = $path;
        $this->profile_photo_name = $fileName;
        
        if($save) {
            $this->save();
        }

        return true;
       
    }




    /**
     * save profile photo
     */
    public function savePhoto($uploadFile, $prefix, $save = false)
    {
        $fileName = $this->generateProfilePhotoName($prefix, $uploadFile->extension());
        $path = $this->profilePhotoSaveAbsPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->profile_photo_path = $path;
        $this->profile_photo_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }



    /**
     * returns profile photo relative path
     */
    public function profilePhotoSaveAbsPath($isAbsolutePath = false)
    {
        return $isAbsolutePath ? public_path('drivers/profile/photos') : 'drivers/profile/photos';
    }


    /**
     * getnerate proflie photo name
     */
    public function generateProfilePhotoName($prefix, $ext)
    {
        $ext = '.'.str_replace('.', '', $ext);
        return $prefix.'_'.md5(uniqid(mt_rand(), true)).'_'.time().$ext;
    }



    
    /**
     * returns profile photo url
     */
    public function profilePhotoUrl()
    {
        return url($this->profile_photo_path.'/'.$this->profile_photo_name);
    }


    /**
     * return string reason why account not activated
     */
    public function reasonNotActivated()
    {
        return 'Admin has deactivated you account';
    }








    /**
     * save push notification device token
     * call this method after driver has been created or driver id exists
     */
    public function addOrUpdateDeviceToken($deviceType, $token, $deviceId = '')
    {
        if(!in_array($deviceType, DeviceToken::DEVICE_TYPES)) {
            $errorCode = 'INVALID_DEVICE_TYPE';
            \Log::info('ADD_DRIVER_DEVICE_TOKEN');
            \Log::info($errorCode.' Type: '.$deviceType);
            return $errorCode;
        }


        if($token == '') {
            $errorCode = 'DEVICE_TOKEN_EMPTY';
            \Log::info('ADD_DRIVER_DEVICE_TOKEN');
            \Log::info($errorCode);
            return $errorCode;
        }

        $deviceTokenModel = app('App\Models\DeviceToken');

        $deviceToken = $deviceTokenModel->where('entity_type', 'DRIVER')
        ->where('entity_id', $this->id)
        ->where('device_type', $deviceType)
        ->where('device_id', $deviceId);

        $deviceToken = $deviceToken->first() ?: new $deviceTokenModel;
        
        $deviceToken->entity_type = 'DRIVER';
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

        $deviceTokens = $deviceTokenModel->where('entity_type', 'DRIVER')
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

        \Log::info('DRIVER SEND PUSH NOTIFICATION');
        \Log::info('TOKENS : '.json_encode($deviceTokens));
        \Log::info('Response : '.$res);

        return true;

    }









}