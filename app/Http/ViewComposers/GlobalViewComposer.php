<?php

namespace App\Http\ViewComposers;

use Illuminate\View\View;
use App\Models\Setting;
use Auth;

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
        ->with('website_fav_icon_url', $this->setting->websiteFavIconUrl())
        ->with('website_name', $this->setting->get('website_name'))
        ->with('website_title', $this->setting->get('website_title'))
        ->with('website_address', $this->setting->get('website_address'))
        ->with('website_contact_number', $this->setting->get('website_contact_number'))
        ->with('website_contact_email', $this->setting->get('website_contact_email'))
        ->with('currency_symbol', $this->setting->get('currency_symbol'))
        ->with('website_company_name', $this->setting->get('website_company_name'))
        ->with('website_copyright', $this->setting->get('website_copyright'))
        ->with('default_timezone', $this->setting->get('default_timezone'))
        ->with('google_maps_api_key', $this->setting->get('google_maps_api_key'))
        ->with('google_maps_api_key_booking_track', $this->setting->get('google_maps_api_key_booking_track'))
        ->with('distance_unit', $this->setting->get('distance_unit'));


        $this->shareAdminDetails($view);

    }


    /**
     * share admin details if admin login
     */
    protected function shareAdminDetails(View $view)
    {
        //check if admin is loggin
        if(!Auth::guard('admin')->user()) {
            return;
        }

        $view->with('admin', Auth::guard('admin')->user());

    }


}