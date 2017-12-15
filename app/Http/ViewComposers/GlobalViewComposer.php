<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Setting;

class GlobalViewComposer
{
    

    /**
     * Create a new profile composer.
     *
     * @return void
     */
    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    /**
     * Bind data to the view.
     *
     * @param  View  $view
     * @return void
     */
    public function compose(View $view)
    {
        /**
         * sharing common variable for all views
         */
        $view->with('website_logo_url', $this->setting->websiteLogoUrl())
        ->with('website_name', $this->setting->get('website_name'))
        ->with('website_address', $this->setting->get('website_address'))
        ->with('website_contact_number', $this->setting->get('website_contact_number'))
        ->with('website_contact_email', $this->setting->get('website_contact_email'))
        ->with('currency_symbol', $this->setting->get('currency_symbol'));
        
    }
}