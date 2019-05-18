<?php

namespace App\Models\Support;

use Illuminate\Database\Eloquent\Model;

class DriverTicket extends Model
{

    const PENDING = 'pending';
    const PROCESSING = 'processing';
    const RESOLVED = 'resolved';

    protected $table = 'driver_support_tickets';

    protected $hidden = ['photo1', 'photo2', 'photo3'];

    protected $appends = ['photo1_url', 'photo2_url' , 'photo3_url'];


    /** 
     * save and return photo path
     */
    public static function savePhoto($file)
    {
        $fileName = self::generatePhotoName('ticket', $file->extension());
        $path = self::generatePhotoPath();
        $file->storeAs($path, $fileName);

        return $path.'/'.$fileName;
    }


    /**
     * generate photo name
     */
    public static function generatePhotoName($prefix, $ext)
    {
        $ext = '.'.str_replace('.', '', $ext);
        return $prefix.'_'.md5(uniqid(mt_rand(), true)).'_'.time().$ext;
    }



    /**
     * generate and return path for saving photo
     */
    public static function generatePhotoPath()
    {
        return 'support/tickets/photos';
    }


    /**
     * get photo 1 url
     */
    public function getPhoto1UrlAttribute()
    {
        return $this->photo1 ? url($this->photo1) : '';
    }


    /**
     * get photo 2 url
     */
    public function getPhoto2UrlAttribute()
    {
        return $this->photo2 ? url($this->photo2) : '';
    }


    /**
     * get photo 3 url
     */
    public function getPhoto3UrlAttribute()
    {
        return $this->photo3 ? url($this->photo3) : '';
    }

}
