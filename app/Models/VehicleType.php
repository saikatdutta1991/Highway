<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleType extends Model
{

    protected $table = 'vehicle_types';

    public function getTableName()
    {
        return $this->table;
    }


    /**
     * get all vehicle codes 
     */
    public function allCodes()
    {
        $vTypes = config('vehicle_types');
        
        //return if null
        if(!$vTypes) { 
            return [];
        }

        $codes = [];
        foreach($vTypes as $vType) {
            $codes[] = $vType['code'];
        } 

        return $codes;
    }




    /**
     * get all 
     */
    public function allTypes()
    {
        $vTypes = config('vehicle_types');
        return $vTypes ? $vTypes : [];

    }




    /**
     * remove vehicle type by code
     */
    public function removeType($code, &$errorCode = '')
    {
        $vType = $this->where('code', $code)->first();

        if(!$vType) {
            $errorCode = 'INVALID';
            return false;
        }

        //deleteing from database
        $vType->forceDelete();

        //fetch all vehicle types and save
        $this->saveToFile($this->all()->toArray());
    
        return true;
        
    }





    /**
     * add new vehicle type and save to settings file
     */
    public function addType($type, &$errorCode = '')
    {
        $code = strtoupper($this->cleanString($type));
        if($this->where('code', $code)->orWhere('name')->exists()) {
           $errorCode = 'EXISTS';
           return false;
        }

        //add new vehicle type
        $vType = new $this;
        $vType->code = $code;
        $vType->name = ucfirst($type);
        $vType->save();

        //fetch all vehicle types and save
        $this->saveToFile($this->all()->toArray());
    
        return $vType;
        
    }



    /**
     * save alll vehicle types to file
     */
    public function saveToFile($array)
    {
        $phpArrayCodingFormat = "<?php \n\n return ";
        $phpArrayCodingFormat .= var_export($array, true);
        $phpArrayCodingFormat .= ";";
        
        $file = config_path('vehicle_types.php');
        file_put_contents($file, $phpArrayCodingFormat);

    }




    /**
     * public function syncWith database
     */
    public function syncWithDatabase()
    {
        //fetch all vehicle types and save to file
        $this->saveToFile($this->all()->toArray());
    }




    /** 
     * clean stgring replaces all ' ' with '_' and remove all special chrs
     */
    protected function cleanString($string)
    {
        $string = str_replace(' ', '_', $string); // Replaces all spaces with hyphens
        $string = preg_replace('/[^A-Za-z0-9\_]/', '', $string); // Removes special chars.
        return $string;
    }



}