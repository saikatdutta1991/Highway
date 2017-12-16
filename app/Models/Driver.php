<?php

namespace App\Models;

use App\Models\DeviceToken;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\PushNotification;

class Driver extends Model
{

    const ACTIVATED = 'ACTIVATED';

    protected $table = 'drivers';
    protected $hidden = [
        'password', 
        'profile_photo_path',
        'profile_photo_name',
        'vehicle_rc_photo_path',
        'vehicle_rc_photo_name',
        'vehicle_contract_permit_photo_path',
        'vehicle_contract_permit_photo_name',
        'vehicle_insurance_certificate_photo_path',
        'vehicle_insurance_certificate_photo_name',
        'vehicle_fitness_certificate_photo_path',
        'vehicle_fitness_certificate_photo_name',
        'vehicle_lease_agreement_photo_path',
        'vehicle_lease_agreement_photo_name',
        'vehicle_photo_1_path',
        'vehicle_photo_1_name',
        'vehicle_photo_2_path',
        'vehicle_photo_2_name',
        'vehicle_photo_3_path',
        'vehicle_photo_3_name',
        'vehicle_photo_4_path',
        'vehicle_photo_4_name',
        'vehicle_commercial_driving_license_photo_path',
        'vehicle_commercial_driving_license_photo_name',
        'vehicle_police_verification_certificate_photo_path',
        'vehicle_police_verification_certificate_name',
        'bank_passbook_photo_path',
        'bank_passbook_photo_name',
        'aadhaar_card_photo_path',
        'aadhaar_card_photo_name'
    ];

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
        $utill = app('UtillRepo');

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
     * save driver other photos like vehicle contract photos, registration certificate photos etc
     */
    public function saveExtraPhotos($vrcp, $vcpp, $vicp, $vfcp, $vlap, $vp1, $vp2, $vp3, $vp4, $vcdlp, $vpvc, $bpp, $acp, $save = false)
    {
        if($vrcp) $this->saveVRCPhoto($vrcp, $save);
        if($vcpp) $this->saveVCPPhoto($vcpp, $save);
        if($vicp) $this->saveVICPhoto($vicp, $save);
        if($vfcp) $this->saveVFCPhoto($vfcp, $save);
        if($vlap) $this->saveVLAPhoto($vlap, $save);
        if($vp1) $this->saveV1Photo($vp1, $save);
        if($vp2) $this->saveV2Photo($vp2, $save);
        if($vp3) $this->saveV3Photo($vp3, $save);
        if($vp4) $this->saveV4Photo($vp4, $save);
        if($vcdlp) $this->saveVCDLPhoto($vcdlp, $save);
        if($vpvc) $this->saveVPVCPhoto($vpvc, $save);
        if($bpp) $this->saveBPPhoto($bpp, $save);
        if($acp) $this->saveACPhoto($acp, $save);

        return true;

    }




    /**
     * save vehicle contract permit photo
     */
    public function saveVCPPhoto($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_vcp_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_contract_permit_photo_path = $path;
        $this->vehicle_contract_permit_photo_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }



    /**
     * save vehicle 2 photo
     */
    public function saveV2Photo($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_v2_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_photo_2_path = $path;
        $this->vehicle_photo_2_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }





    /**
     * save vehicle commercial driving license photo
     */
    public function saveVCDLPhoto($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_vcdl_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_commercial_driving_license_photo_path = $path;
        $this->vehicle_commercial_driving_license_photo_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }



    /**
     * save aadhaar card photo
     */
    public function saveACPhoto($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_ac_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->aadhaar_card_photo_path = $path;
        $this->aadhaar_card_photo_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }




    /**
     * save bank passbook photo
     */
    public function saveBPPhoto($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_bp_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->bank_passbook_photo_path = $path;
        $this->bank_passbook_photo_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }




    /**
     * save vehicle police verification certificate photo
     */
    public function saveVPVCPhoto($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_vpvc_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_police_verification_certificate_photo_path = $path;
        $this->vehicle_police_verification_certificate_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }




    /**
     * save vehicle 4 photo
     */
    public function saveV4Photo($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_v4_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_photo_4_path = $path;
        $this->vehicle_photo_4_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }




    /**
     * save vehicle 3 photo
     */
    public function saveV3Photo($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_v3_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_photo_3_path = $path;
        $this->vehicle_photo_3_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }




    /**
     * save vehicle 1 photo
     */
    public function saveV1Photo($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_v1_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_photo_1_path = $path;
        $this->vehicle_photo_1_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }





    /**
     * save vehicle lease agreement photo
     */
    public function saveVLAPhoto($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_vla_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_lease_agreement_photo_path = $path;
        $this->vehicle_lease_agreement_photo_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }






    /**
     * save vehicle fitness certificate photo
     */
    public function saveVFCPhoto($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_vfc_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_fitness_certificate_photo_path = $path;
        $this->vehicle_fitness_certificate_photo_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }





    /**
     * save vehicle insurance certificate photo
     */
    public function saveVICPhoto($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_vic_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_insurance_certificate_photo_path = $path;
        $this->vehicle_insurance_certificate_photo_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }




    /**
     * save vehicle registration photo
     */
    public function saveVRCPhoto($uploadFile, $save = false)
    {
        $fileName = $this->generatePhotoName('d_vrc_', $uploadFile->extension());
        $path = $this->generatePhotoPath();
        $uploadFile->storeAs($path, $fileName);
        
        $this->vehicle_rc_photo_path = $path;
        $this->vehicle_rc_photo_name = $fileName;

        if($save) {
            $this->save();
        }

        return true;
    }


    /**
     * generate and return path for saving photo
     * returns absolute or relative path base on isAbsolutePath parameter
     */
    public function generatePhotoPath($isAbsolutePath = false)
    {
        return $isAbsolutePath ? public_path('drivers/profile/photos') : 'drivers/profile/photos';
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
        return $this->generatePhotoName($prefix, $ext);
    }



    public function generatePhotoName($prefix, $ext)
    {
        $ext = '.'.str_replace('.', '', $ext);
        return $prefix.'_'.md5(uniqid(mt_rand(), true)).'_'.time().$ext;
    }




    /**
     * returns extra photos url
     */
    public function getExtraPhotosUrl()
    {
        return [
            'vehicle_rc_photo_url' => url($this->vehicle_rc_photo_path.'/'.$this->vehicle_rc_photo_name),
            'vehicle_contract_permit_photo_url' => url($this->vehicle_contract_permit_photo_path.'/'.$this->vehicle_contract_permit_photo_path),
            'vehicle_insurance_certificate_photo_url' => url($this->vehicle_insurance_certificate_photo_path.'/'.$this->vehicle_insurance_certificate_photo_name),
            'vehicle_fitness_certificate_photo_url' => url($this->vehicle_fitness_certificate_photo_path.'/'.$this->vehicle_fitness_certificate_photo_name),
            'vehicle_lease_agreement_photo_url' => url($this->vehicle_lease_agreement_photo_path.'/'.$this->vehicle_lease_agreement_photo_name),
            'vehicle_photo_1_url' => url($this->vehicle_photo_1_path.'/'.$this->vehicle_photo_1_name),
            'vehicle_photo_2_url' => url($this->vehicle_photo_2_path.'/'.$this->vehicle_photo_2_name),
            'vehicle_photo_3_url' => url($this->vehicle_photo_3_path.'/'.$this->vehicle_photo_3_name),
            'vehicle_photo_4_url' => url($this->vehicle_photo_4_path.'/'.$this->vehicle_photo_4_name),
            'vehicle_commercial_driving_license_photo_url' => url($this->vehicle_commercial_driving_license_photo_path.'/'.$this->vehicle_commercial_driving_license_photo_name),
            'vehicle_police_verification_certificate_photo_url' => url($this->vehicle_police_verification_certificate_photo_path.'/'.$this->vehicle_police_verification_certificate_name),
            'bank_passbook_photo_url' => url($this->bank_passbook_photo_path.'/'.$this->bank_passbook_photo_name),
            'aadhaar_card_photo_url' => url($this->aadhaar_card_photo_path.'/'.$this->aadhaar_card_photo_name),
        ];
    }



    
    /**
     * returns profile photo url
     */
    public function profilePhotoUrl()
    {
        return url($this->profile_photo_path.'/'.$this->profile_photo_name);
    }


    
    /**
     * public function get base64 image
     */
    public function getProfilePhotoBase64()
    {
        return app('UtillRepo')->getBase64Image(
            public_path($this->profile_photo_path.'/'.$this->profile_photo_name)
        );
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




    /**
     * find nearby drivers for ride request 
     * used by basically ride request
     * returns laravel query builder
     */
    public function getNearbyDriversBuilder($latitude, $longitude, $radious, $radiousUnit = 'km')
    {

        $utillRepo = app('App\Repositories\Utill');
     
        list($minLat, $maxLat, $minLong, $maxLong) = $utillRepo->getRadiousLatitudeLongitude($latitude, $longitude, $radious, $radiousUnit);

		return $this->where(function ($query) use ($minLat, $maxLat, $minLong, $maxLong) {
            $query->whereBetween($this->table.'.latitude', [$minLat, $maxLat])
            ->whereBetween($this->table.'.longitude', [$minLong, $maxLong]);  
        });
				
	              
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