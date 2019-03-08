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
     * returns vehicle type id by code
     */
    public function getIdByCode($vCode)
    {
        $vTypes = config('vehicle_types') ? : [];
        $vTCollection = collect(config('vehicle_types'));
        $vType = collect($vTCollection->where('code', $vCode)->first());
        return $vType->get('id', 0);
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
        $vTypes = collect(config('vehicle_types'));
        
        return $vTypes ? $vTypes->sortBy('order')->values() : [];

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
     * update service type by id
     */
    public function updateServiceType($newName, &$errorCode = '')
    {
        //check new service new name exists or not for other services
        $service = $this->where('name', $newName)->where('id', '<>', $this->id)->exists();

        if($service) {
           $errorCode = 'EXISTS';
           return false;
        }

        $this->name = ucfirst($newName);
        $this->save();

        //fetch all vehicle types and save
        $this->saveToFile($this->all()->toArray());
    
        return $this;


    }



    /**
     * set service type order
     */
    public static function setOrder($serviceid, $order)
    {
        $service = self::find($serviceid);
        $service->order = $order;
        $service->save();
 
        //fetch all vehicle types and save
        self::saveToFile(self::all()->toArray());
        return true;
    }




    /**
     * add new vehicle type and save to settings file
     */
    public function addType($type, &$errorCode = '')
    {
        $code = app('UtillRepo')->randomChars(8); //generate code for service type        
        if($this->where('code', $code)->orWhere('name', $type)->exists()) {
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
    public static function saveToFile($array)
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
     * sync database with config file
     */
    public function syncWithConfigFile()
    {
        foreach(config('vehicle_types') as $sKey => $sValue) {
            $vType = $this->where('code', $sValue['code'])->first() ?: new $this;
            $vType->code = $sValue['code'];
            $vType->name = $sValue['name'];
            $vType->order = isset($sValue['order']) ? $sValue['order'] : 0;
            $vType->save();
        }
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