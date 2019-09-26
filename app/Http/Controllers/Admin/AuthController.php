<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Repositories\Api;
use Auth;
use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Http\Request;
use Validator;


class AuthController extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, Admin $admin)
    {
        $this->api = $api;
        $this->admin = $admin;
    }



    /** redirect to login page when /admin entered */
    public function redirectLogin()
    {
        return redirect()->route('admin-login'); 
    }


    /**
     * shows login page
     */
    public function showLogin()
    {
        return view('admin.login');
    }



    /**
     * do admin login
     */
    public function doLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|min:3|max:100',
            'password' => 'required|min:6|max:100'
        ]);

        if($validator->fails()) {
            return $this->api->json(false, 'LOGIN_FAILED', $validator->errors()->all()[0]);
        }



        $admin = $this->admin->where('email', $request->email)->first();
        if(!$admin) {
            return $this->api->json(false, 'LOGIN_FAILED', 'Invalid email');
        }


        if(!password_verify($request->password, $admin->password)) {
            return $this->api->json(false, 'LOGIN_FAILED', 'Invalid password. Try again');
        }

        Auth::guard('admin')->login($admin);
        return $this->api->json(true, 'LOGIN_SUCCESS', 'Logged in successfully', [
            'intended_url' => redirect()->intended(route('admin-dashboard'))->getTargetUrl()
        ]);
    }





    /**
     * logout admin
     */
    public function doLogout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin-login');
    }






}
