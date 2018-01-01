<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Driver;
use Validator;
use App\Models\VehicleType;
use App\Models\Setting;


class Service extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Setting $setting, VehicleType $vehicleType, Api $api, Driver $driver)
    {
        $this->setting = $setting;
        $this->vehicleType = $vehicleType;
        $this->api = $api;
        $this->driver = $driver;
    }



    /**
     * show service lists
     */
    public function showServices()
    {
        $services = $this->vehicleType->allTypes();

        //calculate count of each services by drivers
        foreach($services as $index => $service) {
            $services[$index]['used_by_driver'] = $this->driver->where('vehicle_type', $service['code'])->count();
        }


        return view('admin.services', compact('services'));
    }



}


