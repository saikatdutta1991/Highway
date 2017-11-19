<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});







/**
 * admin route goes here
 */
Route::group(['prefix' => 'admin'], function(){


    Route::group(['middleware' => 'adminGuest'], function(){
        
        Route::get('/', function(){ return redirect()->route('admin-login'); });
        Route::get('login', 'Admin\AuthController@showLogin')->name('admin-login');
        Route::post('login', 'Admin\AuthController@doLogin');

    });



    Route::group(['middleware' => 'adminAuth'], function(){

        Route::get('dashboard', 'Admin\Dashboard@showDashboard')->name('admin-dashboard');
        Route::get('logout', 'Admin\AuthController@doLogout');

    });


});















/***
 * this routes for debug purpose. Should be removed on production before going live
 */

Route::get('sync-vehicle-types', function(){

    app('App\Models\VehicleType')->syncWithDatabase();

});


Route::get('sync-settings', function(){

    app('App\Models\Setting')->syncWithDatabase();

});



Route::get('sync-settings-with-file', function(){

    app('App\Models\Setting')->syncWithConfigFile();

});