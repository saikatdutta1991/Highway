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

        Route::get('users', 'Admin\User@showUsers')->name('admin-users');
        Route::get('users/{user_id}', 'Admin\User@showUser');
        Route::get('users/send-pushnotification', 'Admin\User@sendPushnotification');
        Route::post('users/{user_id}/update', 'Admin\User@updateUserProfile');
        Route::post('users/{user_id}/reset-password', 'Admin\User@resetUserPassword');

        Route::get('drivers', 'Admin\Driver@showDrivers')->name('admin-drivers');
        Route::get('drivers/map', 'Admin\Driver@showDriversOnMap')->name('admin-drivers-map');
        Route::get('drivers/nearby', 'Admin\Driver@getNearbyDrivers')->name('admin-nearby-drivers');
        Route::get('drivers/send-pushnotification', 'Admin\Driver@sendPushnotification');
        
        Route::post('drivers/{driver_id}/approve/{is_approve}', 'Admin\Driver@approveDriver');
        Route::get('drivers/{driver_id}', 'Admin\Driver@showDriver');
        Route::post('drivers/{driver_id}/change-photo', 'Admin\Driver@changeDriverPhoto');
        Route::post('drivers/{driver_id}/update', 'Admin\Driver@updateDriverProfile');
        Route::post('drivers/{driver_id}/reset-password', 'Admin\Driver@resetDriverPassword');

        Route::get('services', 'Admin\Service@showServices');
        Route::post('services/add', 'Admin\Service@addService');
        Route::get('services/{service_id}/ridefare', 'Admin\Service@getRideFare');
        Route::post('services/{service_id}/ridefare', 'Admin\Service@createOrUpdateRideFare');
        
        Route::get('settings/email', 'Admin\Setting@showEmailSetting');
        Route::post('settings/email/save', 'Admin\Setting@saveEmailSettings');
        Route::post('settings/email/test', 'Admin\Setting@testEmail');
        Route::get('settings/sms', 'Admin\Setting@showSmsSetting');
        Route::post('settings/sms/save', 'Admin\Setting@saveSmsSetting');
        Route::post('settings/sms/test', 'Admin\Setting@testSms');
        Route::get('settings/firebase', 'Admin\Setting@showFirebaseSetting');
        Route::post('settings/firebase/save', 'Admin\Setting@saveFirebaseSetting');
        Route::get('settings/facebook', 'Admin\Setting@showFacebookSetting');
        Route::post('settings/facebook/save', 'Admin\Setting@saveFacebookSetting');
        Route::get('settings/google', 'Admin\Setting@showGoogleSetting');
        Route::post('settings/google/save', 'Admin\Setting@saveGoogleSetting');
        Route::post('settings/google/map-key/save', 'Admin\Setting@saveGoogleMapKey');
        Route::get('settings/general', 'Admin\Setting@showGeneralSetting');
        Route::post('settings/general/website/save', 'Admin\Setting@saveGeneralSettings');
        Route::post('settings/general/website/logo/save', 'Admin\Setting@saveWebsiteLogo');
        Route::post('settings/general/website/favicon/save', 'Admin\Setting@saveWebsiteFavicon');
        
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


Route::get('send-sms', function(){

    dd(app('App\Repositories\Otp')->sendOTP('+91', '9093036897', 'hello', 1));

});


Route::get('send-email', function(){

    
    
    $user = App\Models\User::where('email', 'saikatdutta1991@gmail.com')->first();
    app('App\Repositories\Email')->sendNewUserWelcomeEmail($user);


});


Route::get('send_push', function(){
    
    /* $pushHelper = new \App\Repositories\PushNotification;
        $res = $pushHelper->setTitle('Test Push notification')
        ->setBody('Message Body')
        ->setIcon('logo')
        ->setClickAction('')
        ->setCustomPayload(['custom_data' => ''])
        ->setPriority(\App\Repositories\PushNotification::HIGH)
        ->setContentAvailable(true)
        ->setDeviceTokens(request()->token)
        ->push();

        dd($res); */


});

