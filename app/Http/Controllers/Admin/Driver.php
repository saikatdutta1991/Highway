<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use App\Repositories\Email;
use Hash;
use Illuminate\Http\Request;
use App\Models\Driver as DriverModel;
use Validator;
use App\Models\VehicleType;


class Driver extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(VehicleType $vehicleType, Email $email, Api $api, DriverModel $driver)
    {
        $this->vehicleType = $vehicleType;
        $this->email = $email;
        $this->api = $api;
        $this->driver = $driver;
    }



    /**
     * shows admin dashboard
     */
    public function showDrivers(Request $request)
    {

        $drivers = $this->driver->take(100);
        
        try {


            //if search_by & keyword presend then only apply filter
            $search_by = $request->search_by;
            $skwd = $request->skwd;
            $location_name = $request->location_name;
            $latitude = $request->latitude;
            $longitude = $request->longitude;

            //check location name available then search by location
            if($location_name != '' && $search_by == 'location' && $latitude != '' && $longitude != '') {
                $drivers = $this->driver->getNearbyDriversBuilder($latitude, $longitude, $request->radius);
            } else if($request->search_by != '' && $request->search_by != 'location' && $request->skwd != '') {
                $drivers = $drivers->where($request->search_by, 'like', '%'.$request->skwd.'%')->orWhere('lname', 'like', '%'.$request->skwd.'%');
            }


            //check if order_by is present
            $order_by = ($request->order_by == '' || $request->order_by == 'created_at') ? 'created_at' : $request->order_by;
            //if order(asc | desc) not present take desc default
            $order = ($request->order == '' || $request->order == 'desc') ? 'desc' : 'asc';
            $drivers = $drivers->orderBy($order_by, $order);


            $drivers = $drivers->paginate(100)->setPath('drivers');

        } catch(\Exception $e){
            //if any error happens take default
            $drivers = $this->driver->take(100)->paginate(2)->setPath('drivers');
        }
        
        return view('admin.drivers', compact('drivers', 'order_by', 'order', 'search_by', 'skwd', 'location_name', 'latitude', 'longitude'));

    }





    /**
     * send push notification to drivers by server-send-event javascript
     */
    public function sendPushnotification(Request $request)
    {
        
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use($request){

            $title = $request->title;
            $message = $request->message;
            $currentCount = 0;

            /**
             * if send_all on then send push notification to all drivers
             */
            if($request->has('send_all') && $request->send_all == 'on') {

                //count all drivers
                $driversCount = $this->driver->count();
                

                //chunking drivers from db 100 each chunk
                $this->driver->select(['id'])->chunk(100, function ($drivers) use($driversCount, $title, $message, $currentCount) {
                    foreach ($drivers as $driver) {
                        $driver->sendPushNotification($title, $message);
                        
                        //calculate percentage
                        $percent = (++$currentCount / $driversCount) * 100;
                        $json = json_encode(['total' => $driversCount, 'done' => $currentCount, 'percent' => $percent]);
                        echo "data: {$json}\n\n";
                        ob_flush();
                        flush();
                    }

                });

            }
            //send_all not present to send one by one selected drivers 
            else {

                //count all selected drivers
                $drivers = $driver = $this->driver->whereIn('id', explode('|', $request->ids))->select(['id'])->get();;
                $driversCount = $drivers->count();


                foreach ($drivers as $driver) {
                    
                    $driver->sendPushNotification($title, $message);
                    
                    //calculate percentage
                    $percent = (++$currentCount / $driversCount) * 100;
                    $json = json_encode(['total' => $driversCount, 'done' => $currentCount, 'percent' => $percent]);
                    echo "data: {$json}\n\n";
                    ob_flush();
                    flush();
                }


            }



        });
            
            

        $response->headers->set('Content-Type', 'text/event-stream');
        return $response;

    }






    /**
     * approved or disapprove driver
     */
    public function approveDriver(Request $request)
    {
        $driver = $this->driver->find($request->driver_id);
        $isApprove = intval($request->is_approve);
        

        //approve driver
        if($isApprove == 1) {
            $driver->is_approved = 1;

            //make driver avaialbe
            $driver->is_available = 1;

            $driver->save();
            
            //send email driver has approved
            $this->email->sendDriverAccountApproved($driver);
            return $this->api->json(true, 'DRIVER_APPROVED', 'Driver approved');
        }
        //disapprove driver and take message(reason) from admin 
        else if($isApprove == 0) {

            $driver->is_approved = 0;
            $driver->save();

            //send email driver has disapproved
            $message = $request->message;
            $this->email->sendDriverAccountDisapproved($driver, $message);

            return $this->api->json(true, 'DRIVER_DISAPPROVED', 'Driver disapproved');
        }


    }




    /**
     * show and search drivers on map
     */
    public function showDriversOnMap(Request $request)
    {
        return view('admin.drivers_on_map');
    }






    /**
     * show driver and edit
     */
    public function showDriver(Request $request)
    {
        $vehicleTypes = $this->vehicleType->allTypes();
        $driver = $this->driver->find($request->driver_id);
        return view('admin.edit_driver', compact('driver', 'vehicleTypes'));
    }




    /**
     * get nearby drivers
     */
    public function getNearbyDrivers(Request $request)
    {
        $cLat = $request->current_latitude;
        $cLng = $request->current_longitude;
        $radius = $request->has('radius') ? $request->radius : 100;
        $drivers = $this->driver->getNearbyDriversBuilder($cLat, $cLng, $radius)
        ->select(['id', 'latitude', 'longitude', 'vehicle_number', 'fname', 'lname', 'full_mobile_number'])->get();

        return $this->api->json(true, 'DRIVERS', 'Drivers fetched', [
            'drivers' => $drivers,
        ]);

    }



    /**
     * change driver photo
     */
    public function changeDriverPhoto(Request $request)
    {
        $driver = $this->driver->find($request->driver_id);
        //this checking wont happend because admin will not hack the website
        if(!$driver) {return;}

        $validator = \Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if($validator->fails()) {
            $e = $validator->errors();
            $msg = [];
            ($e->has('photo')) ? $msg['photo'] = $e->get('photo')[0] : '';
            return $this->api->json(false, 'PHOTO_VALIDATION_ERROR', 'Photo validation error', $msg);
        }

        $driver->savePhoto($request->photo, 'driver_');
        $driver->save();

        return $this->api->json(true, 'PHOTO_CHANGED', 'Photo changed successfully', [
            'profile_photo_url' => $driver->profilePhotoUrl()
        ]);

    }




}
