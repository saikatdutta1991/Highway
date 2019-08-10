<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HirePackage;;
use Validator;


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
        $package->hours = $request->hours;
        $package->charge = $request->charge;
        $package->per_hour_charge = $request->per_hour_charge;
        $package->night_charge = $request->night_charge;
        $package->grace_time = $request->grace_time;
        $package->night_hours = ($request->night_from  == '' || $request->night_to == '') ? "" : "{$request->night_from}-{$request->night_to}";
        $package->save();
        
        return redirect()->route("admin.hiring.package.add.show", [ 'id' => $package->id, 'success' => 1]);
    }




    /** get hours list */
    protected function getHoursList() 
    {
        $hours = [];
        for($i = 0; $i <= 23; $i++) {

            $thf = "";
            if($i == 0) {
                $thf = "12 AM";
            } else if($i == 12) {
                $thf = "12 PM";
            } else if ($i <= 11) {
                $h = str_pad($i, 2, '0', STR_PAD_LEFT);
                $thf = "{$h} AM";
            } else if ($i >= 13) {
                $h = str_pad($i - 12, 2, '0', STR_PAD_LEFT);
                $thf = "{$h} PM";
            }

            $hours[$i] = $thf;
        }

        return $hours;
    }



    /** show view where can package be added */
    public function showHiringPackageAdd(Request $request)
    {
        $package = HirePackage::find($request->id);
        $hours = $this->getHoursList();
        return view("admin.hiring.add_package", [
            'hours' => $hours, "package" => $package
        ]);
    }




    /** show list of packages, where admin can add new packages */
    public function showHiringPackages()
    {
        dd('showHiringPackages');
    }



}
