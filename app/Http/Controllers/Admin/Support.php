<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Validator;


class Support extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Setting $setting, Api $api)
    {
        $this->setting = $setting;
        $this->api = $api;
    }




    /**
     * show settings page
     */
    public function showSettings()
    {
        $support_ticket_notify_emails = $this->setting->get('support_ticket_notify_emails');
        return view('admin.support.settings', compact('support_ticket_notify_emails'));
    }



    /**
     * save general settings
     */
    public function saveGeneralSettings(Request $request)
    {
        $this->setting->set('support_ticket_notify_emails', trim($request->support_ticket_notify_emails));
        return $this->api->json(true, 'GENERAL_SETTINGS_SAVED', 'General settings saved');
    }

}
