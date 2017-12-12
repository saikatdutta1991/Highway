<?php


/**
 * routes for user api
 * routes are alredy prefixed by '/api'
 */
Route::group(['prefix' => '/v1/user'], function(){

    Route::post('register', 'Apis\User\UserRegister@doRegister');
    Route::post('login', 'Apis\User\UserRegister@doLogin');
    
    Route::get('vehicle-types', 'Apis\User\UserRegister@getVehicleTypes');

    //social login routes
    Route::post('facebook', 'Apis\User\Facebook@authenticate');
    Route::post('google', 'Apis\User\Google@authenticate');


    Route::group(['middleware' => 'userApiAuth'], function(){

        Route::post('otp/send', 'Apis\User\UserRegister@sendOtp');
        Route::post('otp/verify', 'Apis\User\UserRegister@verifydOtp');

        Route::get('profile', 'Apis\User\UserProfile@getUserProfile');
        Route::post('profile/update', 'Apis\User\UserProfile@updateUserProfile');

        Route::post('estimate-price/{vehicle_type_id}', 'Apis\User\PriceCalculator@estimatePrice');

        Route::get('nearby-drivers/{lat_long}/{vehicle_type?}', 'Apis\User\RideRequest@getNearbyDrivers');

        Route::get('ride-request/payment-modes', 'Apis\User\RideRequest@getPaymentModes');
        Route::post('ride-request/update-payment-mode', 'Apis\User\RideRequest@updatePaymentMode');

        Route::get('ride-request/check', 'Apis\User\RideRequest@checkRideRequest');
        Route::post('ride-request/{ride_request_id}/cancel', 'Apis\User\RideRequest@cancelRideRequest');

        Route::post('ride-request/initiate', 'Apis\User\RideRequest@initiateRideRequest');

    });




});



/**
 * routes for driver apis
 * routes are alredy prefixed by '/api'
 */
Route::group(['prefix' => '/v1/driver'], function(){

    Route::post('register', 'Apis\Driver\DriverAuth@doRegister');
    Route::post('login', 'Apis\Driver\DriverAuth@doLogin');
    Route::get('vehicle-types', 'Apis\Driver\DriverAuth@getVehicleTypes');


    //social login routes
    Route::post('facebook', 'Apis\Driver\Facebook@authenticate');
    Route::post('google', 'Apis\Driver\Google@authenticate');


    //driver's authenticated routes
    Route::group(['middleware' => 'driverApiAuth'], function(){

        Route::post('otp/send', 'Apis\Driver\DriverAuth@sendOtp');
        Route::post('otp/verify', 'Apis\Driver\DriverAuth@verifydOtp');


        Route::get('profile', 'Apis\Driver\DriverProfile@getDriverProfile');
        Route::post('profile/update', 'Apis\Driver\DriverProfile@updateDriverProfile');

        Route::post('ride-request/accept', 'Apis\Driver\RideRequest@acceptRideRequest');



    });



});
