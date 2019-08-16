<?php


/**
 * routes for user api
 * routes are alredy prefixed by '/api'
 */
Route::group(['prefix' => '/v1/user'], function(){


    /** get coupon code */
    Route::get('coupons', 'Apis\User\Coupon@getCoupons');


    /** password reset apis */
    Route::post('password-reset-request', 'Apis\User\UserRegister@sendPasswordReset');
    Route::post('password-reset', 'Apis\User\UserRegister@resetPassword');

    Route::post('register', 'Apis\User\UserRegister@doRegister');
    Route::post('login', 'Apis\User\UserRegister@doLogin');
    
    Route::get('vehicle-types', 'Apis\User\UserRegister@getVehicleTypes');

    //social login routes
    Route::post('facebook', 'Apis\User\Facebook@authenticate');
    Route::post('google', 'Apis\User\Google@authenticate');

    Route::group(['prefix' => 'referral'], function(){
        Route::get('verify', 'Apis\User\Referral@verifyReferralCode');
    });


    /** trip user authorization removed apis */
    Route::get('trips/source-destination-list', 'Apis\User\Trip@getSourceDestList');
    /** end trip user authorization removed apis */




    Route::group(['middleware' => ['userApiAuth']], function() {

        Route::group([ "prefix" => "hiring"], function(){
            Route::get("packages", "Apis\User\Hiring@getHiringPackages");
            Route::post('request', "Apis\User\Hiring@createRequest");
            Route::get("bookings", "Apis\User\Hiring@getBookings");
        });

        Route::group(['prefix' => 'support'], function() {
            Route::get('tickets', 'Apis\User\Support@getTickets');
            Route::post('tickets/create', 'Apis\User\Support@createTicket');
        });



        Route::post('push-token/update', 'Apis\User\UserProfile@updatePushToken');
        Route::post('otp/send', 'Apis\User\UserRegister@sendOtp');
        Route::post('otp/verify', 'Apis\User\UserRegister@verifydOtp');

        Route::get('profile', 'Apis\User\UserProfile@getUserProfile');
        Route::post('profile/update', 'Apis\User\UserProfile@updateUserProfile');

        Route::post('estimate-price/{vehicle_type_id}', 'Apis\User\PriceCalculator@estimatePrice');

        Route::get('nearby-drivers/{lat_long}/{vehicle_type?}', 'Apis\User\RideRequest@getNearbyDrivers');

        Route::get('ride-request/payment-modes', 'Apis\User\RideRequest@getPaymentModes');
        Route::get('ride-request/check', 'Apis\User\RideRequest@checkRideRequest');
        Route::post('ride-request/{ride_request_id}/cancel', 'Apis\User\RideRequest@cancelRideRequest');
        Route::post('ride-request/initiate', 'Apis\User\RideRequest@initiateRideRequest');
        Route::post('ride-request/{ride_request_id}/rate-driver', 'Apis\User\RideRequest@rateDriver');
        Route::get('ride-requests/histories', 'Apis\User\RideRequest@getHistories')->name('ride_request_histories');


        /**
         * rasorpay payment apis
         */
        Route::post('ride-requests/paymentmode/update', 'Apis\User\RideRequest@updatePaymentMode');
        Route::post('ride-requests/{ride_request_id}/razorpay/init', 'Apis\User\RideRequest@initRazorpay');
        Route::post('ride-requests/{ride_request_id}/razorpay/pay', 'Apis\User\RideRequest@makeRazorpayPayment');



        Route::group(['prefix' => 'referral'], function(){
            Route::get('info', 'Apis\User\Referral@getReferralInfo');
        });





        /**
         * intercity trip routes
         */
        Route::group(['prefix' => 'trips'], function(){

            Route::get('search', 'Apis\User\Trip@searchTrips');
            Route::post('book', 'Apis\User\Trip@bookTrip');
            Route::get('bookings', 'Apis\User\Trip@getBookedTrips');
            Route::get('{trip_id}/booking', 'Apis\User\Trip@getBookingByTrip');
            Route::get('bookings/{booking_id}/razorpay/init', 'Apis\User\Trip@initRazorpay');
            Route::post('bookings/{booking_id}/razorpay/pay', 'Apis\User\Trip@makeRazorpayPayment');
            Route::post('bookings/{booking_id}/rating', 'Apis\User\Trip@rateTripDriver');
            Route::get('bookings/unrated', 'Apis\User\Trip@getUnratedBookings');
            Route::post('bookings/{booking_id}/cancel', 'Apis\User\Trip@cancelTrip');

            /** validate coupon code route */
            Route::post('{trip_id}/coupon-code-validate', 'Apis\User\PriceCalculator@validateCouponCodeForTrip');

        }); 


    });




});



/**
 * routes for driver apis
 * routes are alredy prefixed by '/api'
 */
Route::group(['prefix' => '/v1/driver'], function(){

    /**
     * password reset apis
     */
    Route::post('password-reset-request', 'Apis\Driver\DriverAuth@sendPasswordReset');
    Route::post('password-reset', 'Apis\Driver\DriverAuth@resetPassword');

    Route::post('register', 'Apis\Driver\DriverAuth@doRegister');
    Route::post('login', 'Apis\Driver\DriverAuth@doLogin');
    Route::get('vehicle-types', 'Apis\Driver\DriverAuth@getVehicleTypes');


    //social login routes
    Route::post('facebook', 'Apis\Driver\Facebook@authenticate');
    Route::post('google', 'Apis\Driver\Google@authenticate');


    /** get all admin created routes */
    Route::get('trips/locations', 'Apis\Driver\Trip@getAllLocations');
    Route::get('trips/routes', 'Apis\Driver\Trip@getTripRoutes');


    Route::group(['prefix' => 'referral'], function(){
        Route::get('verify', 'Apis\Driver\Referral@verifyReferralCode');
    });


    //driver's authenticated routes
    Route::group(['middleware' => ['driverApiAuth']], function(){

        Route::group([ "prefix" => "hiring"], function(){
            Route::get('requests/{id?}', "Apis\Driver\Hiring@getRequests");
            Route::post("request/action", "Apis\Driver\Hiring@actionRequest");
            Route::get("bookings", "Apis\Driver\Hiring@getBookings");
            Route::post("bookings/action/driver-start", "Apis\Driver\Hiring@driverStart");
            Route::post("bookings/action/start", "Apis\Driver\Hiring@startRide");
            Route::post("bookings/action/end", "Apis\Driver\Hiring@endRide");
        });

        Route::get('dashboard-details', 'Apis\Driver\Dashboard@getDetails')->name('driver.dashboard.details');
        Route::get('payouts', 'Apis\Driver\Dashboard@getPayoutDetails')->name('driver.dashboard.payout.details');
        Route::get('account', 'Apis\Driver\Dashboard@getDriverAccount')->name('driver.dashboard.adcount');

        Route::group(['prefix' => 'support'], function() {
            Route::get('tickets', 'Apis\Driver\Support@getTickets');
            Route::post('tickets/create', 'Apis\Driver\Support@createTicket');
        });

        Route::post('otp/send', 'Apis\Driver\DriverAuth@sendOtp');
        Route::post('otp/verify', 'Apis\Driver\DriverAuth@verifydOtp');

        Route::post('push-token/update', 'Apis\Driver\DriverProfile@updatePushToken');
        Route::get('profile', 'Apis\Driver\DriverProfile@getDriverProfile');
        Route::post('profile/update', 'Apis\Driver\DriverProfile@updateDriverProfile');
        Route::post('bank/update', 'Apis\Driver\DriverProfile@updateBank');

        Route::get('ride-request/check', 'Apis\Driver\RideRequest@checkRideRequest');
        Route::post('ride-request/accept', 'Apis\Driver\RideRequest@acceptRideRequest');
        Route::post('ride-request/{ride_request_id}/cancel', 'Apis\Driver\RideRequest@cancelRideRequest');
        Route::post('ride-request/{ride_request_id}/change-ride-status', 'Apis\Driver\RideRequest@changeRideRequestStatus');
        Route::post('ride-request/{ride_request_id}/start-trip', 'Apis\Driver\RideRequest@startRideRequest');
        Route::post('ride-request/{ride_request_id}/end-trip', 'Apis\Driver\RideRequest@endRideRequest');
        Route::post('ride-request/{ride_request_id}/rate-user', 'Apis\Driver\RideRequest@rateUser');

        Route::get('ride-requests/histories', 'Apis\Driver\RideRequest@getHistories')->name('ride_request_histories');



        Route::group(['prefix' => 'referral'], function(){
            Route::get('info', 'Apis\Driver\Referral@getReferralInfo');
        });


        /**
         * intercity trip routes
         */
        Route::group(['prefix' => 'trips'], function(){

            Route::post('create', 'Apis\Driver\Trip@createTrip');
            Route::get('/', 'Apis\Driver\Trip@getTrips');
            Route::get('{trip_id}', 'Apis\Driver\Trip@getTripDetails');
            Route::post('{trip_id}/start', 'Apis\Driver\Trip@startTrip');
            Route::post('{trip_id}/points/{point_id}/reached', 'Apis\Driver\Trip@driverReachedTripPoint');
            Route::post('{trip_id}/bookings/users/boarded', 'Apis\Driver\Trip@userBoarded');
            Route::post('{trip_id}/bookings/users/{user_id}/rate', 'Apis\Driver\Trip@driverGiveRatingToBookedUsers');
            Route::post('{trip_id}/complete', 'Apis\Driver\Trip@completeTrip');
            Route::post('{trip_id}/cancel', 'Apis\Driver\Trip@cancelTrip');
        }); 


        Route::get('logout', 'Apis\Driver\DriverAuth@getLogout')->name('driver.logout');


    });



});
