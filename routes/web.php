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

Route::get('/', 'Web\HomeController@showHomepage')->name('home');
Route::get('track-booking/{bookingid}', 'Web\HomeController@trackBooking')->name('track-booking');
Route::get('track-booking/{bookingid}/progress', 'Web\HomeController@trackBookingProgress')->name('track-booking-progress');
Route::get('track-booking/{bookingid}/map', 'Web\HomeController@trackBookingMap')->name('track-booking-map');
Route::get('track-booking/{bookingid}/boarding-point-route', 'Web\HomeController@trackBookingBoardingPointRoute')->name('bookings.track.boarding-point-route');
Route::get('offers', 'Admin\Coupon@showOffers')->name('offers'); /**coupon offers page */
Route::get('privacy-policy', 'Web\HomeController@showPrivacyPolicy')->name('privacy-policy');
Route::get('terms', 'Web\HomeController@showTerms')->name('terms');
Route::get('driver-terms', 'Web\HomeController@showDriverTerms')->name('driver.terms');
Route::get('cancellation-policy', 'Web\HomeController@showCancellationPolicy')->name('cancellation.policy');
Route::get('referrals/{referrer_code}', 'Web\HomeController@redirectReferral')->name('referrals.redirect'); //?referrer_code
Route::get('price-estimate', 'Web\HomeController@showPriceEstimate')->name('priceestimate.show');
Route::get('price-estimate/list', 'Web\HomeController@getEstimatePrice')->name('priceesimate.list');
Route::get('common-configs', 'Web\HomeController@getCommonConfigs')->name('common.configs');
Route::get("hiring/bookings/{booking_id}/track", "Web\HomeController@showTrackDriverBooking")->name("hiring.bookings.track");
Route::get("hiring/bookings/{booking_id}/track/location", "Web\HomeController@getDriverBookingTrackLocation")->name("hiring.bookings.track.location");








/**
 * admin route goes here
 */
Route::group(['prefix' => 'admin'], function(){

    Route::group(['middleware' => 'adminGuest'], function(){
        
        Route::get('/', 'Admin\AuthController@redirectLogin')->name("admin.redirect.login");
        Route::get('/login', 'Admin\AuthController@showLogin')->name('admin-login');
        Route::post('/login', 'Admin\AuthController@doLogin')->name('admin-login');

    });

    Route::group(['middleware' => 'adminAuth'], function(){


        Route::group(["prefix" => "hiring"], function(){
            Route::get("package/add", "Admin\Hiring@showHiringPackageAdd")->name("admin.hiring.package.add.show");
            Route::post("package/add", "Admin\Hiring@addHiringPackage")->name("admin.hiring.package.add");
            Route::get("packages", "Admin\Hiring@showHiringPackages")->name("admin.hiring.packages.show");
            Route::get("bookings", "Admin\Hiring@showUserBookings")->name("admin.hiring.bookings");
            Route::post("bookings/assign/driver", "Admin\Hiring@assignDriver")->name("admin.hiring.booking.assign.driver");
            Route::get("bookings/{booking_id}/details/template", "Admin\Hiring@getBookingDetails")->name("admin.hiring.booking.details.template");
        });

        Route::group(['prefix' => 'drivers'], function(){
            Route::get('accounts', 'Admin\AccountManagement@showDriverAccounts')->name('admin.drivers.accounts');
            Route::get('accounts/recharge', 'Admin\AccountManagement@showAccountRecharge')->name('admin.drivers.accounts.recharge');
            Route::post('accounts/recharge/process', 'Admin\AccountManagement@processAccountRecharge')->name('admin.drivers.accounts.recharge.process');
        });

        Route::get('dashboard', 'Admin\Dashboard@showDashboard')->name('admin-dashboard');
        Route::get('payouts', 'Admin\Payout@showFilteredPayouts')->name('admin.payouts.show');
        Route::get('payouts/settings', 'Admin\Payout@showPayoutSettings')->name('admin.payouts.settings.show');
        Route::post('payouts/settings', 'Admin\Payout@savePayoutSettings')->name('admin.payouts.settings.save');

        Route::group(['prefix' => 'support'], function(){
            Route::get('settings', 'Admin\Support@showSettings')->name('admin.support.show.settings');
            Route::post('settings/general/save', 'Admin\Support@saveGeneralSettings')->name('admin.support.general.save');
            
            Route::get('user/tickets', 'Admin\Support@getUserTickets')->name('admin.support.user.tickets');
            Route::post('user/tickets/{ticket_number}/update', 'Admin\Support@updateUserTicket')->name('admin.support.user.ticket.update');

            Route::get('driver/tickets', 'Admin\Support@getDriverTickets')->name('admin.support.driver.tickets');
            Route::post('driver/tickets/{ticket_number}/update', 'Admin\Support@updatedriverTicket')->name('admin.support.driver.ticket.update');
        });

        Route::group(['prefix' => 'contents'], function(){
            Route::get('privacy-policy', 'Admin\ContentManagement@showPrivacyPolicy')->name('admin.show.content.privacy-policy');
            Route::post('privacy-policy/save', 'Admin\ContentManagement@savePrivacyPolicy')->name('admin.save.content.privacy-policy');
            Route::get('terms', 'Admin\ContentManagement@showTerms')->name('admin.show.content.terms');
            Route::post('terms/save', 'Admin\ContentManagement@saveTerms')->name('admin.save.content.terms');
            Route::get('driver-terms', 'Admin\ContentManagement@showDriverTerms')->name('admin.show.content.driver.terms');
            Route::post('driver-terms/save', 'Admin\ContentManagement@saveDriverTerms')->name('admin.save.content.driver.terms');
            Route::get('cancellation-policy', 'Admin\ContentManagement@showCancellationPolicy')->name('admin.show.content.cancellation.policy');
            Route::post('cancellation-policy/save', 'Admin\ContentManagement@saveCancellationPolicy')->name('admin.save.content.cancellation.policy');
        });

        Route::group(['prefix' => 'promotions'], function(){
            Route::get('/', 'Admin\Promotion@showPromotions')->name('admin.promotions');
            Route::post('/{promotion_id}/delete', 'Admin\Promotion@deletePromotion')->name('admin.promotion.delete');
            Route::get('/{promotion_id}/preview', 'Admin\Promotion@previewPromotionEmail')->name('admin.promotion.email.preview');
            Route::get('/add', 'Admin\Promotion@showAddPromotion')->name('admin.show.add.promotion');
            Route::get('/edit/{promotion_id}', 'Admin\Promotion@showEditPromotion')->name('admin.show.edit.promotion');
            Route::post('/save', 'Admin\Promotion@savePromotion')->name('admin.save.promotion');
            Route::get('/sample-email-template', 'Admin\Promotion@getSampleEmailTemplate')->name('admin.promotion.sample-template');
            Route::post('{promotion_id}/broadcast', 'Admin\Promotion@broadcastPromotion')->name('admin.promotion.broadcast');
        });


        /** user routes */
        Route::group(['prefix' => 'users'], function(){
            Route::get('/', 'Admin\User@showUsers')->name('admin-users');
            Route::get('/send-pushnotification', 'Admin\User@sendPushnotification')->name('admin.users.pushnotification.send');
            Route::get('/{user_id}', 'Admin\User@showUser')->name('admin.show.user');        
            Route::post('/{user_id}/update', 'Admin\User@updateUserProfile')->name('admin.user.update');
            Route::post('/{user_id}/reset-password', 'Admin\User@resetUserPassword')->name('admin.user.password.reset');
        });


        /** driver routes */
        Route::group(["prefix" => 'drivers'], function(){
            Route::get("search", "Admin\Driver@searchDriver")->name("admin.driver.search");
            Route::get('/', 'Admin\Driver@showDrivers')->name('admin-drivers');
            Route::get('/map', 'Admin\Driver@showDriversOnMap')->name('admin-drivers-map');
            Route::get('/nearby', 'Admin\Driver@getNearbyDrivers')->name('admin-nearby-drivers');
            Route::get('/send-pushnotification', 'Admin\Driver@sendPushnotification')->name('admin.drivers.pushnotification.send');
            Route::post('/{driver_id}/approve/{is_approve}', 'Admin\Driver@approveDriver')->name('admin.driver.approve');
            Route::get('/{driver_id}', 'Admin\Driver@showDriver')->name('admin.show.driver');
            Route::post('/{driver_id}/bank', 'Admin\Driver@updateBank')->name('admin.driver.bank.update');
            Route::post('/{driver_id}/change-photo', 'Admin\Driver@changeDriverPhoto')->name('admin.driver.update.photo');
            Route::post('/{driver_id}/update', 'Admin\Driver@updateDriverProfile')->name('admin.driver.update');
            Route::post('/{driver_id}/reset-password', 'Admin\Driver@resetDriverPassword')->name('admin.driver.password.reset');
            Route::get('fake/locations', 'Admin\Driver@showFakeLocations')->name('admin.driver.fake.locations.show');
            Route::post('fake/locations/enable', 'Admin\Driver@enableFakeLocations')->name('admin.driver.fake.locations.enable');
            Route::post('fake/locations/save', 'Admin\Driver@saveFakeLocations')->name('admin.driver.fake.locations.save');
        });

        Route::get('rides/intracity', 'Admin\RideRequest@showIntracityRideRequests')->name('admin.rides.city');
        Route::post('rides/intracity/{ride_request_id}/cancel', 'Admin\RideRequest@cancelRide')->name('admin.rides.city.cancel');
        Route::post('rides/intracity/{ride_request_id}/complete', 'Admin\RideRequest@completeRide')->name('admin.rides.city.complete');
        Route::get('rides/intracity/{ride_request_id}/details', 'Admin\RideRequest@showIntracityRideRequestDetails')->name('admin.rides.city.details');

        /** service routes */
        Route::group(["prefix" => "services"], function(){
            Route::get('/', 'Admin\Service@showServices')->name('admin.services');
            Route::post('/add', 'Admin\Service@addService')->name('admin.services.add');
            Route::get('/{service_id}/ridefare', 'Admin\Service@getRideFare');
            Route::post('/{service_id}/ridefare', 'Admin\Service@createOrUpdateRideFare');
            Route::post('/tax/save', 'Admin\Service@saveRideTaxPercentage')->name('admin.services.tax.save');
            Route::post('/cancellation-charge/save', 'Admin\Service@saveRideRequestCancellationCharge')->name('admin.services.cancellation-charge.save');
            Route::post('/driver-cancel-ride-request-limit/save', 'Admin\Service@saveDriverCancelRideRequestLimit')->name('admin.service.driver-cancel-ride-request-limit');
            Route::post('/driver-search-radius/save', 'Admin\Service@saveDriverSearchRadius')->name('admin.service.driver-search-radius.save');
        });
        
        Route::group(['prefix' => 'settings'], function(){

            Route::get('email', 'Admin\Setting@showEmailSetting')->name('admin.settings.email');
            Route::post('email/save', 'Admin\Setting@saveEmailSettings')->name('admin.settings.email.save');
            Route::post('email/test', 'Admin\Setting@testEmail')->name('admin.settings.email.test');
            
            Route::get('sms', 'Admin\Setting@showSmsSetting')->name('admin.settings.sms');
            Route::post('sms/save', 'Admin\Setting@saveSmsSetting')->name('admin.settings.sms.save');
            Route::post('sms/test', 'Admin\Setting@testSms')->name('admin.settings.sms.test');
            
            Route::get('firebase', 'Admin\Setting@showFirebaseSetting')->name('admin.settings.firebase');
            Route::post('firebase/save', 'Admin\Setting@saveFirebaseSetting')->name('admin.settings.firebase.save');
            
            Route::get('facebook', 'Admin\Setting@showFacebookSetting')->name('admin.settings.facebook');
            Route::post('facebook/save', 'Admin\Setting@saveFacebookSetting')->name('admin.settings.facebook.save');
            
            Route::get('google', 'Admin\Setting@showGoogleSetting')->name('admin.settings.google');
            Route::post('google/save', 'Admin\Setting@saveGoogleSetting')->name('admin.settings.google.save');
            Route::post('google/map-key/save', 'Admin\Setting@saveGoogleMapKey')->name('admin.settings.google.mapkey.save');

            Route::get('general', 'Admin\Setting@showGeneralSetting')->name('admin.settings.general');
            Route::post('general/website/save', 'Admin\Setting@saveGeneralSettings')->name('admin.settings.general.website.save');
            Route::post('general/website/logo/save', 'Admin\Setting@saveWebsiteLogo')->name('admin.settings.general.website.logo.save');
            Route::post('general/website/favicon/save', 'Admin\Setting@saveWebsiteFavicon')->name('admin.settings.general.website.favicon.save');
            
            Route::get('razorpay', 'Admin\Setting@showRazorpaySetting')->name('admin.settings.razorpay');
            Route::post('razorpay/save', 'Admin\Setting@saveRazorpaySetting')->name('admin.settings.razorpay.save');
            
            Route::get('seo-management', 'Admin\Setting@showSeoSetting')->name('admin.show.seo');
            Route::post('seo-management', 'Admin\Setting@saveSeoSetting')->name('admin.save.seo');

        });

        /**referral routes */
        Route::group(['prefix' => 'referral'], function(){

            Route::get('settings', 'Admin\Referral@showReferralSetting')->name('admin.settings.referral');
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
            Route::get('locations/{location_id}/points', 'Admin\Trip@showLocation')->name('admin.routes.locations.points.show');
            Route::post('locations/{location_id}/points/create', 'Admin\Trip@createLocationPoints')->name('admin.add-locatin-points');
            Route::get('/', 'Admin\Trip@showRoutes')->name('admin.show-all-routes'); //show all admin routes
            Route::get('new', 'Admin\Trip@showNewRoute')->name('admin.show-new-route');
            Route::get('edit/{route_id}', 'Admin\Trip@showEditRoute')->name('admin.show-edit-route');
            Route::post('new', 'Admin\Trip@addNewRoute')->name('admin.add_new_route');//add new trip route

            Route::group(['prefix' => 'trips'], function(){

                Route::get('/', 'Admin\Trip@showTrips')->name('admin.show.trips');
                Route::get('bookings', 'Admin\Trip@showBookings')->name('admin.show.bookings');

                Route::get('canceled-bookings', 'Admin\Trip@showCanceledBookings')->name('admin.show_canceled_bookings');
                Route::post('bookings/{booking_id}/refund-full', 'Admin\Trip@fullRefundTripBooking');
                Route::post('bookings/{booking_id}/refund-partial', 'Admin\Trip@partialRefundTripBooking');
            });

        });
        /** trip routes end */

        Route::get('logout', 'Admin\AuthController@doLogout')->name('admin.logout');

    });


});
