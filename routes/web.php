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

Route::get('/', 'Web\HomeController@showHomepage');
Route::get('track-booking/{bookingid}', 'Web\HomeController@trackBooking');



/**
 * coupon offers page
 */
Route::get('offers', 'Admin\Coupon@showOffers')->name('offers');




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
        Route::get('users/send-pushnotification', 'Admin\User@sendPushnotification');
        Route::get('users/{user_id}', 'Admin\User@showUser');        
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


        Route::get('rides/intracity', 'Admin\RideRequest@showIntracityRideRequests');
        Route::get('rides/intracity/{ride_request_id}/details', 'Admin\RideRequest@showIntracityRideRequestDetails');



        Route::get('services', 'Admin\Service@showServices');
        Route::post('services/add', 'Admin\Service@addService');
        Route::get('services/{service_id}/ridefare', 'Admin\Service@getRideFare');
        Route::post('services/{service_id}/ridefare', 'Admin\Service@createOrUpdateRideFare');
        Route::post('services/tax/save', 'Admin\Service@saveRideTaxPercentage');
        Route::post('services/cancellation-charge/save', 'Admin\Service@saveRideRequestCancellationCharge');
        

        Route::group(['prefix' => 'settings'], function(){

            Route::get('email', 'Admin\Setting@showEmailSetting');
            Route::post('email/save', 'Admin\Setting@saveEmailSettings');
            Route::post('email/test', 'Admin\Setting@testEmail');
            Route::get('sms', 'Admin\Setting@showSmsSetting');
            Route::post('sms/save', 'Admin\Setting@saveSmsSetting');
            Route::post('sms/test', 'Admin\Setting@testSms');
            Route::get('firebase', 'Admin\Setting@showFirebaseSetting');
            Route::post('firebase/save', 'Admin\Setting@saveFirebaseSetting');
            Route::get('facebook', 'Admin\Setting@showFacebookSetting');
            Route::post('facebook/save', 'Admin\Setting@saveFacebookSetting');
            Route::get('google', 'Admin\Setting@showGoogleSetting');
            Route::post('google/save', 'Admin\Setting@saveGoogleSetting');
            Route::post('google/map-key/save', 'Admin\Setting@saveGoogleMapKey');
            Route::get('general', 'Admin\Setting@showGeneralSetting');
            Route::post('general/website/save', 'Admin\Setting@saveGeneralSettings');
            Route::post('general/website/logo/save', 'Admin\Setting@saveWebsiteLogo');
            Route::post('general/website/favicon/save', 'Admin\Setting@saveWebsiteFavicon');
            Route::get('razorpay', 'Admin\Setting@showRazorpaySetting');
            Route::post('razorpay/save', 'Admin\Setting@saveRazorpaySetting');

        });



        /**referral routes */
        Route::group(['prefix' => 'referral'], function(){

            Route::get('settings', 'Admin\Referral@showReferralSetting');
            Route::post('save/enable', 'Admin\Referral@saveEnable')->name('admin.referral_save_enable');
            Route::post('save/bonus', 'Admin\Referral@saveBonusAmount')->name('admin.referral_save_bonus');
            Route::get('users', 'Admin\Referral@showReferralUsers')->name('admin.show_referral_users');

        });
        /**referral routes end */


        /** coupon code routes */
        Route::group(['prefix' => 'coupons'], function(){

            Route::get('/', 'Admin\Coupon@showCoupons')->name('admin.coupons.show');
            Route::get('/add', 'Admin\Coupon@showAddCoupon')->name('admin.coupons.show.add-new');
            Route::post('/add', 'Admin\Coupon@addCoupon')->name('admin.coupons.add-new');
            Route::get('/add/{coupon_id}', 'Admin\Coupon@showEditCoupon')->name('admin.coupons.show.edit');
            Route::post('/add/{coupon_id}', 'Admin\Coupon@updateCoupon')->name('admin.coupons.update');

        });
        /** coupon code routes end */






        /** trip routes */
        Route::group(['prefix' => 'routes'], function(){
            
            Route::get('locations', 'Admin\Trip@showLocations')->name('admin.show_all_locations');
            Route::post('locations/create', 'Admin\Trip@createLocation')->name('admin.create_location');
            Route::post('locations/update', 'Admin\Trip@updateLocation')->name('admin.update_location');
            Route::get('locations/{location_id}/points', 'Admin\Trip@showLocation');
            Route::post('locations/{location_id}/points/create', 'Admin\Trip@createLocationPoints')->name('admin.add-locatin-points');
            Route::get('/', 'Admin\Trip@showRoutes')->name('admin.show-all-routes'); //show all admin routes
            Route::get('new', 'Admin\Trip@showNewRoute')->name('admin.show-new-route');
            Route::get('edit/{route_id}', 'Admin\Trip@showEditRoute')->name('admin.show-edit-route');
            Route::post('new', 'Admin\Trip@addNewRoute')->name('admin.add_new_route');//add new trip route


            Route::get('canceled-bookings', 'Admin\Trip@showCanceledBookings')->name('admin.show_canceled_bookings');
            Route::post('trips/bookings/{booking_id}/refund-full', 'Admin\Trip@fullRefundTripBooking');
            Route::post('trips/bookings/{booking_id}/refund-partial', 'Admin\Trip@partialRefundTripBooking');

        });
        /** trip routes end */

        
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

    
    //dd(app('App\Repositories\Email')->sendCommonEmail('saikatdutta1991@gmail.com', '$welcomename', '$subject', '$messageBody'));


});


Route::get('send_push', function(){
    
    $pushHelper = new \App\Repositories\PushNotification;
        $res = $pushHelper->setTitle('Test Push notification')
        ->setBody('Message Body')
        ->setIcon('logo')
        ->setClickAction('')
        ->setCustomPayload(['custom_data' => ''])
        ->setPriority(\App\Repositories\PushNotification::HIGH)
        ->setContentAvailable(true)
        ->setDeviceTokens(request()->token)
        ->push();

        dd($res);


});

