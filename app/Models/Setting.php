<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    protected $table = 'settings';

    public function getTableName()
    {
        return $this->table;
    }



    /**
     * get setting value by key
     */
    public function get($key)
    {
        return config('settings.'.$key);
    }


    public function getAllSettings()
    {
        $settings = config('settings');
        return $settings?:[];
    }



    /**
     *  add new setting
     */
    public function set($key, $value)
    {
        $setting = $this->where('key', $key)->first();
        $setting = $setting ?: new $this;
        
        $setting->key = $key;
        $setting->value = $value;

        $setting->save();

        //fetch all settings and save
        $settings = [];
        foreach($this->all() as $s) {
            $settings[$s->key] = $s->value;
        }

        $this->saveToFile($settings);
    
        return $setting;
        
    }


    /**
     * save alll vehicle types to file
     */
    public function saveToFile($array)
    {
        $phpArrayCodingFormat = "<?php \n\n return ";
        $phpArrayCodingFormat .= var_export($array, true);
        $phpArrayCodingFormat .= ";";
        
        $file = config_path('settings.php');
        file_put_contents($file, $phpArrayCodingFormat);

    }





    /**
     * sync database with config file
     */
    public function syncWithConfigFile()
    {
        foreach($this->getAllSettings() as $sKey => $sValue) {
            $s = $this->where('key', $sKey)->first() ?: new $this;
            $s->key = $sKey;
            $s->value = $sValue;
            $s->save();
        }
    }





    /**
     * sysc with database
     */
    public function syncWithDatabase()
    {
        //fetch all settings and save
        $settings = [];
        foreach($this->all() as $s) {
            $settings[$s->key] = $s->value;
        }

        $this->saveToFile($settings);   
    }





    /**
     * returns website logo path
     */
    public function generateWebsiteLogoPath()
    {
        return $this->get('website_logo_path');
    }

  


    /**
     * returns website logo url
     */
    public function websiteLogoUrl()
    {
        return url($this->get('website_logo_path') . '/' . $this->get('website_logo_name'));
    }



    /**
     * upload and save website logo
     */
    public function saveWebsiteLogo($uploadFile, $prefix = 'logo_')
    {
        $fileName = app('UtillRepo')->generatePhotoName($prefix, $uploadFile->extension());
        $path = $this->get('website_logo_path');
        $uploadFile->storeAs($path, $fileName);
        
        $this->set('website_logo_name', $fileName);
        return url($this->get('website_logo_path') . '/' . $fileName);
    }
    





    /**
     * returns website favicon url
     */
    public function websiteFavIconUrl()
    {
        return url($this->get('website_fav_icon_path') . '/' . $this->get('website_fav_icon_name'));
    }



    /**
     * upload and save website logo
     */
    public function saveWebsiteFavicon($uploadFile, $prefix = 'favicon_')
    {
        $fileName = app('UtillRepo')->generatePhotoName($prefix, $uploadFile->extension());
        $path = $this->get('website_fav_icon_path');
        $uploadFile->storeAs($path, $fileName);
        
        $this->set('website_fav_icon_name', $fileName);
        return url($this->get('website_fav_icon_path') . '/' . $fileName);
    }



}