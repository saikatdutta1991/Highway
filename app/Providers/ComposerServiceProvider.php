<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // Using class based composers...
        View::composer('*', 'GlobalViewComposer');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

        /**
         * making GlobalViewComposer singleton 
         * so for every view this class object will be initialized only once
         */
        $this->app->singleton('GlobalViewComposer', function ($app) {
            return $app->make('App\Http\ViewComposers\GlobalViewComposer');
        });

    }
}