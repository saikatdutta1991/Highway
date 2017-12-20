<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;
use App\Models\Driver as DriverModel;
use Validator;


class Driver extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(DriverModel $driver)
    {
        $this->driver = $driver;
    }



    /**
     * shows admin dashboard
     */
    public function showDrivers(Request $request)
    {

        $drivers = $this->driver->take(100);
        
        try {

            //check if order_by is present
            $order_by = ($request->order_by == '' || $request->order_by == 'created_at') ? 'created_at' : $request->order_by;
            //if order(asc | desc) not present take desc default
            $order = ($request->order == '' || $request->order == 'desc') ? 'desc' : 'asc';
            $drivers = $drivers->orderBy($order_by, $order);


            //if search_by & keyword presend then only apply filter
            if($request->search_by != '' && $request->skwd != '') {
                $drivers = $drivers->where($request->search_by, 'like', '%'.$request->skwd.'%');
            }

            $drivers = $drivers->paginate(100)->setPath('drivers');

        } catch(\Exception $e){
            //if any error happens take default
            $drivers = $this->driver->take(100)->paginate(2)->setPath('drivers');
        }
        
        

        return view('admin.drivers', compact('drivers'));

    }



}
