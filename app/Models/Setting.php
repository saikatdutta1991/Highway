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




}