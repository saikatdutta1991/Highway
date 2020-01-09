<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Api;
use App\Models\Driver;
use App\Models\User;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setting = app('App\Models\Setting');
        $this->setEmailSettings();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {

        /**
         * making App\Models\Setting singleton
         */
        $this->app->singleton('App\Models\Setting', function ($app) {
            return new \App\Models\Setting();
        });


        /**
         * make App\Repositories\Utill singleton
         */
        $this->app->singleton('UtillRepo', function ($app) {
            return $app->make('App\Repositories\Utill');
        });
        $this->app->singleton('App\Repositories\Utill', function ($app) {
            return new \App\Repositories\Utill(app('App\Models\Setting'));
        });


        
        /**
         * make App\Repositories\Email single ton
         */
        $this->app->singleton('App\Repositories\Email', function ($app) {
            return new \App\Repositories\Email(app('App\Models\Setting'));
        });

    }

    /**
    *   This code will set Email config taking information from settings 
    */  
    protected function setEmailSettings()
    {

        $driver = $this->setting->get('mail_driver');
        
        if ($driver == 'mandrill') {

            config([
                'mail.driver'              => 'mandrill',
                'mail.host'                => $this->setting->get('mandrill_host'),
                'mail.port'                => intval($this->setting->get('mandrill_port')),
                'mail.username'            => $this->setting->get('mandrill_username'),   
                'services.mandrill.secret' => $this->setting->get('mandrill_secret'),
                'mail.encryption'          => $this->setting->get('mandrill_encryption')
            ]);


        } else if ($driver == 'smtp') {

            config([
                'mail.driver'       => 'smtp',
                'mail.host'         => $this->setting->get('smtp_host'),
                'mail.port'         => intval($this->setting->get('smtp_port')),
                'mail.username'     => $this->setting->get('smtp_username'),
                'mail.password'     => $this->setting->get('smtp_password'),
                'mail.encryption'   => $this->setting->get('smtp_encryption')
            ]);

        } else if($driver == 'mailgun') {


             config([
                'mail.driver'       => 'mailgun',
                'mail.host'         => $this->setting->get('mailgun_host'),
                'mail.port'         => intval($this->setting->get('mailgun_port')),
                'mail.username'     => $this->setting->get('mailgun_username'),
                'mail.password'     => $this->setting->get('mailgun_password'),
                'mail.encryption'   => $this->setting->get('mailgun_encryption'),
            ]);


            config([
                'services.mailgun.domain' => $this->setting->get('mailgun_domain'),
                'services.mailgun.secret' => $this->setting->get('mailgun_secret')
            ]);


        }

    }


}
