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