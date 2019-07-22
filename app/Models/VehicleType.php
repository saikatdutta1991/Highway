<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Cache;
use Log;

class VehicleType extends Model
{

    protected $table = 'vehicle_types';

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
     * store all services in to cache as array
     */
    protected static function updateServicesCache()
    {
        /** fetch all services from database */
        $services = VehicleType::all();

        /** update setting in cache */
        Cache::forever("services", $services->toArray(), Setting::cacheExpirationTime());
    }





    /**
     * returns vehicle type id by code
     */
    public static function getIdByCode($vCode)
    {
        $services = VehicleType::allTypes();
        $service = $services->where('code', $vCode)->first();
        return $service ? $service['id'] : 0;
    }


    /**
     * get all vehicle codes 
     */
    public static function allCodes()
    {
        $services = VehicleType::allTypes();
        return $services->pluck('code')->toArray();
    }




    /**
     * returns all services as array from cache and make it collection 
     */
    public static function allTypes()
    {
        $servicesArray = Cache::rememberForever("services", function() {

            Log::info("VehicleType::allTypes() -> Retriving setting from db");

            /** fetch all services from database */
            $services = VehicleType::all();
            return $services->toArray();

        });

        return collect($servicesArray)->sortBy('order')->values();

    }




    /**
     * remove vehicle type by code
     */
    public function removeType($code, &$errorCode = '')
    {
        $vType = VehicleType::where('code', $code)->first();

        if(!$vType) {
            $errorCode = 'INVALID';
            return false;
        }

        /** deleteing from database */
        $vType->forceDelete();

        /** updating cache */
        VehicleType::updateServicesCache();
    
        return true;
        
    }




    /**
     * update service type by id
     */
    public function updateServiceType($newName, &$errorCode = '')
    {
        //check new service new name exists or not for other services
        $service = VehicleType::where('name', $newName)->where('id', '<>', $this->id)->exists();

        if($service) {
           $errorCode = 'EXISTS';
           return false;
        }

        $this->name = ucfirst($newName);
        $this->save();

        /** updating cache */
        VehicleType::updateServicesCache();
    
        return $this;


    }



    /**
     * set is_highway_enabled 
     */
    public static function enableHighway($serviceid, $enable)
    {
        $service = VehicleType::find($serviceid);
        $service->is_highway_enabled = $enable;
        $service->save();
 
        /** updating cache */
        VehicleType::updateServicesCache();

        return true;
    }



    /**
     * set service type order
     */
    public static function setOrder($serviceid, $order)
    {
        $service = VehicleType::find($serviceid);
        $service->order = $order;
        $service->save();
 
        /** updating cache */
        VehicleType::updateServicesCache();

        return true;
    }




    /**
     * add new vehicle type and save to settings file
     */
    public function addType($code, $type, &$errorCode = '')
    {
        //$code = app('UtillRepo')->randomChars(8); //generate code for service type        
        if(VehicleType::where('code', $code)->orWhere('name', $type)->exists()) {
           $errorCode = 'EXISTS';
           return false;
        }

        //add new vehicle type
        $vType = new $this;
        $vType->code = $code;
        $vType->name = ucfirst($type);
        $vType->save();

        /** updating cache */
        VehicleType::updateServicesCache();
    
        return $vType;
        
    }

}