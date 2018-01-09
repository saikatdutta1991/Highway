<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use App\Repositories\Email;
use Hash;
use Illuminate\Http\Request;
use App\Models\RideRequest as Ride;
use App\Models\User as UserModel;
use Validator;
use App\Models\Setting;


class User extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Ride $rideRequest, Setting $setting, Email $email, Api $api, UserModel $user)
    {
        $this->rideRequest = $rideRequest;
        $this->setting = $setting;
        $this->email = $email;
        $this->api = $api;
        $this->user = $user;
    }



    /**
     * shows users
     */
    public function showUsers(Request $request)
    {

        $users = $this->user->with('rideRequests');
        
        try {


            //if search_by & keyword presend then only apply filter
            $search_by = $request->search_by;
            $skwd = $request->skwd;

            //filter users by search_by col name
            if($request->search_by != '' && $request->skwd != '') {
                
                //if search by is name then use fname and lname
                if($request->search_by == 'name' ) {
                    $users = $users->where('fname', 'like', '%'.$request->skwd.'%')->orWhere('lname', 'like', '%'.$request->skwd.'%');
                } 
                //filter by col name directly
                else {
                    $users = $users->where($request->search_by, 'like', '%'.$request->skwd.'%');
                }

            }


            //check if order_by is present
            $order_by = ($request->order_by == '' || $request->order_by == 'created_at') ? 'created_at' : $request->order_by;
            //if order(asc | desc) not present take desc default
            $order = ($request->order == '' || $request->order == 'desc') ? 'desc' : 'asc';
            $users = $users->orderBy($order_by, $order);


        } catch(\Exception $e){}
        
        $users = $users->paginate(100)->setPath('users');

        $todaysUsers = $this->user->where('created_at', date('Y-m-d'))->count();
        $thisMonthUsers = $this->user->where('created_at', 'like', date('Y-m').'%')->count();
        $thisYearUsers = $this->user->where('created_at', 'like', date('Y').'%')->count();
        $totalUsers = $this->user->count();


        return view('admin.users', compact(
            'users', 'order_by', 'order', 'search_by', 'skwd',
            'todaysUsers', 'thisMonthUsers', 'thisYearUsers', 'totalUsers'
        ));

    }







    /**
     * send push notification to users by server-send-event javascript
     */
    public function sendPushnotification(Request $request)
    {
        
        $response = new \Symfony\Component\HttpFoundation\StreamedResponse(function() use($request){

            $title = $request->title;
            $message = $request->message;
            $currentCount = 0;

            /**
             * if send_all on then send push notification to all users
             */
            if($request->has('send_all') && $request->send_all == 'on') {

                //count all users
                $usersCount = $this->user->count();
                

                //chunking users from db 100 each chunk
                $this->user->select(['id'])->chunk(100, function ($users) use($usersCount, $title, $message, $currentCount) {
                    foreach ($users as $user) {
                        $user->sendPushNotification($title, $message);
                        
                        //calculate percentage
                        $percent = (++$currentCount / $usersCount) * 100;
                        $json = json_encode(['total' => $usersCount, 'done' => $currentCount, 'percent' => $percent]);
                        echo "data: {$json}\n\n";
                        ob_flush();
                        flush();
                    }

                });

            }
            //send_all not present to send one by one selected users 
            else {

                //count all selected users
                $users = $user = $this->user->whereIn('id', explode('|', $request->ids))->select(['id'])->get();;
                $usersCount = $users->count();


                foreach ($users as $user) {
                    
                    $user->sendPushNotification($title, $message);
                    
                    //calculate percentage
                    $percent = (++$currentCount / $usersCount) * 100;
                    $json = json_encode(['total' => $usersCount, 'done' => $currentCount, 'percent' => $percent]);
                    echo "data: {$json}\n\n";
                    ob_flush();
                    flush();
                }


            }



        });
            
            

        $response->headers->set('Content-Type', 'text/event-stream');
        return $response;

    }




}
