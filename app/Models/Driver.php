<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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



}