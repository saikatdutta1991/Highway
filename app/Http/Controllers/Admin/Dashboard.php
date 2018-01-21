<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;
use App\Models\RideRequest;
use App\Models\RideRequestInvoice;
use App\Models\User;
use App\Models\Driver;
use Validator;


class Dashboard extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(RideRequestInvoice $rideRequestInvoice, RideRequest $rideRequest, User $user, Driver $driver, Api $api, Admin $admin)
    {
        $this->rideRequestInvoice = $rideRequestInvoice;
        $this->rideRequest = $rideRequest;
        $this->user = $user;
        $this->driver = $driver;
        $this->api = $api;
        $this->admin = $admin;
    }



    /**
     * shows admin dashboard
     */
    public function showDashboard()
    {
        $totalUsers = $this->user->count();

        //get users whoever accessed apis witing 5min
        $date = new \DateTime;
        $date->modify('-5 minutes');
        $fd = $date->format('Y-m-d H:i:s');
        $totalOnlineUsers = $this->user->where('last_access_time', '>=', $fd)->count();


        $totalDrivers = $this->driver->count();
        $totalOnlineDrivers = $this->driver->where('is_connected_to_socket', 1)->count();

        $totalRideRequests = $this->rideRequest->count();

        //total cash payments
        $totalCashPayments = $this->rideRequestInvoice->where('payment_mode', RideRequest::CASH)->sum('total');
        $totalOnlinePayments = $this->rideRequestInvoice->where('payment_mode', RideRequest::ONLINE)->sum('total');
        $totalRevenue = $this->rideRequestInvoice->sum('total');


        //latest 5 users
        $laterUsers = $this->user->orderBy('created_at', 'desc')->take(5)->get();
        //latest 5 drivers
        $laterDrivers = $this->driver->orderBy('created_at', 'desc')->take(5)->get();


        $todaysUsers = $this->user->where('created_at', date('Y-m-d'))->count();
        $pastSevenDaysUsers = $this->user->where('created_at', date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d')))))->where('created_at', '<=', date('Y-m-d'))->count();
        $thisMonthUsers = $this->user->where('created_at', 'like', date('Y-m').'%')->count();
        $thisYearUsers = $this->user->where('created_at', 'like', date('Y').'%')->count();

        $todaysDrivers = $this->driver->where('created_at', date('Y-m-d'))->count();
        $pastSevenDaysDrivers = $this->driver->where('created_at', date('Y-m-d', strtotime('-7 day', strtotime(date('Y-m-d')))))->where('created_at', '<=', date('Y-m-d'))->count();
        $thisMonthDrivers = $this->driver->where('created_at', 'like', date('Y-m').'%')->count();
        $thisYearDrivers = $this->driver->where('created_at', 'like', date('Y').'%')->count();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalDrivers', 'totalRideRequests', 'totalOnlineUsers', 'totalOnlineDrivers',
            'totalCashPayments', 'totalOnlinePayments', 'totalRevenue',
            'laterUsers', 'laterDrivers',
            'todaysUsers', 'pastSevenDaysUsers', 'thisMonthUsers', 'thisYearUsers',
            'todaysDrivers', 'pastSevenDaysDrivers', 'thisMonthDrivers', 'thisYearDrivers'
        ));
    }



}
