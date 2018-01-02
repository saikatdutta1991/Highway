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
        
        //sort by descdending created timestamp
        $services = collect($services)->sortByDesc('created_at')->toArray();

        //calculate count of each services by drivers
        foreach($services as $index => $service) {
            $services[$index]['used_by_driver'] = $this->driver->where('vehicle_type', $service['code'])->count();
        }


        return view('admin.services', compact('services'));
    }



    /**
     * add new service type
     */
    public function addService(Request $request)
    {

        //validation service name
        if($request->service_name == '' || !in_array($request->_action, ["update", 'add'])) {
            return $this->api->json(false, 'MISSING_PARAMTERS', 'Missing parameters');
        }

        switch ($request->_action) {
            case 'add':
                
                $error = '';
                $serviceType = $this->vehicleType->addType($request->service_name, $error);

                //check if service already exists
                if($serviceType === false && $error == 'EXISTS') {
                    return $this->api->json(false, 'EXISTS', 'Service already exists');
                }

                return $this->api->json(true, 'ADDED', 'Service created successfully', [
                    'service_type' => $serviceType
                ]);
            
                break;

            case 'update':
                $error = '';
                $serviceType = $this->vehicleType->find($request->service_id);
                $serviceType = $serviceType->updateServiceType($request->service_name, $error);
                
                //check if service already exists
                if($serviceType === false && $error == 'EXISTS') {
                    return $this->api->json(false, 'EXISTS', 'Service name already exists for another');
                }

                return $this->api->json(true, 'UPDATED', 'Service updated successfully', [
                    'service_type' => $serviceType
                ]);
            
                break;
        }

        


    }




}


