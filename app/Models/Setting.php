<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use Log;

class Setting extends Model
{

    protected $table = 'settings';

    public function getTableName()
    {
        return $this->table;
    }


    /**
     * returns cache expiration time in minutes
     */
    protected static function cacheExpirationTime()
    {
        return 60 * 24 * 365;
    }


    /**
     * get setting value by key
     */
    public static function get($key)
    {
        return Cache::rememberForever("settings.{$key}", function() use($key) {

            Log::info("Setting::get() -> Retriving setting from db key : {$key}");

            $setting = Setting::where('key', $key)->select('value')->first();
            return $setting ? $setting->value : '';
        });
    }



    /**
     * add new settings in db
     * store in cache for later use
     */
    public static function set($key, $value)
    {   
        Log::info("Setting::get() -> Storing setting into db key : {$key}");

        /** fetch setting from db or create new */
        $record = Setting::where('key', $key)->first() ?: new Setting;
        $record->key = $key;
        $record->value = $value;
        $record->save();

        /** update setting in cache */
        Cache::forever("settings.{$key}", $value, Setting::cacheExpirationTime());

        return $record;
    }



    /**
     * returns website logo path
     */
    public function generateWebsiteLogoPath()
    {
        return Setting::get('website_logo_path');
    }

  


    /**
     * returns website logo url
     */
    public function websiteLogoUrl()
    {
        return url(Setting::get('website_logo_path') . '/' . Setting::get('website_logo_name'));
    }



    /**
     * upload and save website logo
     */
    public function saveWebsiteLogo($uploadFile, $prefix = 'logo_')
    {
        $fileName = app('UtillRepo')->generatePhotoName($prefix, $uploadFile->extension());
        $path = Setting::get('website_logo_path');
        $uploadFile->storeAs($path, $fileName);
        
        Setting::set('website_logo_name', $fileName);
        return url(Setting::get('website_logo_path') . '/' . $fileName);
    }
    





    /**
     * returns website favicon url
     */
    public function websiteFavIconUrl()
    {
        return url(Setting::get('website_fav_icon_path') . '/' . Setting::get('website_fav_icon_name'));
    }



    /**
     * upload and save website logo
     */
    public function saveWebsiteFavicon($uploadFile, $prefix = 'favicon_')
    {
        $fileName = app('UtillRepo')->generatePhotoName($prefix, $uploadFile->extension());
        $path = Setting::get('website_fav_icon_path');
        $uploadFile->storeAs($path, $fileName);
        
        Setting::set('website_fav_icon_name', $fileName);
        return url(Setting::get('website_fav_icon_path') . '/' . $fileName);
    }



}