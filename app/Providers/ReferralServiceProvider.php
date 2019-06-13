<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Referral\Observers\UserObserver;
use App\Models\Referral\Observers\DriverObserver;
use App\Models\User;
use App\Models\Driver;

class ReferralServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /**
         * observer when user and driver is being created only if referral module enabled
         */
        try {
            if(app('App\Repositories\Referral')->isEnabled()) {
                User::observe(UserObserver::class);
                Driver::observe(DriverObserver::class);
            }
        } catch(\Exception $e) {
            \Log::info('App\ProvReferralServiceProvideriders::boot ' . $e->getMessage());
        }


        
        
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        /**
         * making App\Repositories\Referral singleton
         */
        $this->app->singleton('App\Repositories\Referral', function ($app) {
            return new \App\Repositories\Referral(
                $app->make('App\Repositories\Api'),
                $app->make('App\Models\Setting'),
                $app->make('App\Models\Referral\ReferralCode'),
                $app->make('App\Models\Referral\ReferralHistory')
            );
        });

    }

    

}
