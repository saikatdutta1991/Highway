<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HirePackage;;
use Validator;
use App\Repositories\Utill;


class Hiring extends Controller
{
    public function __construct(Api $api)
    {
        $this->api = $api;
    }


    /** add package */
    public function addHiringPackage(Request $request)
    {
        $package = HirePackage::find($request->id) ?: new HirePackage;
        $package->name = $request->name;
        $package->hours = $request->hours;
        $package->charge = $request->charge;
        $package->per_hour_charge = $request->per_hour_charge;
        $package->night_charge = $request->night_charge;
        $package->grace_time = $request->grace_time;
        $package->night_hours = ($request->night_from  == '' || $request->night_to == '') ? "" : "{$request->night_from}-{$request->night_to}";
        $package->save();
        
        return redirect()->route("admin.hiring.package.add.show", [ 'id' => $package->id, 'success' => 1]);
    }



    /** show view where can package be added */
    public function showHiringPackageAdd(Request $request)
    {
        $package = HirePackage::find($request->id);
        $hours = Utill::getHoursList();
        return view("admin.hiring.add_package", [
            'hours' => $hours, "package" => $package
        ]);
    }




    /** show list of packages, where admin can add new packages */
    public function showHiringPackages()
    {
        $packages = HirePackage::orderBy('created_at', 'desc')->get();
        $hours = Utill::getHoursList();
        return view("admin.hiring.packages", [ "packages" => $packages, "hours" => $hours ]);
    }



}
