<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;
use Validator;


class Dashboard extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, Admin $admin)
    {
        $this->api = $api;
        $this->admin = $admin;
    }



    /**
     * shows admin dashboard
     */
    public function showDashboard()
    {
        return view('admin.dashboard');
    }



}
