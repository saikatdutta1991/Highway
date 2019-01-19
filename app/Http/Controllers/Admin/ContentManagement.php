<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;


class ContentManagement extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api, Setting $setting)
    {
        $this->api = $api;
        $this->setting = $setting;
    }


    /**
     * show privacy policy management
     */
    public function showPrivacyPolicy()
    {
        $privacy_policy = $this->setting->get('privacy_policy');
        return view('admin.contents.privacy_policy', compact('privacy_policy'));
    }


    /**
     * save privacy policy
     */
    public function savePrivacyPolicy(Request $request)
    {
        $this->setting->set('privacy_policy', $request->privacy_policy);
        return $this->api->json(true, 'PRIVACY_POLICY_SAVED', 'Privacy policy saved');
    }


    /**
     * show Terms management
     */
    public function showTerms()
    {
        $terms = $this->setting->get('terms');
        return view('admin.contents.terms', compact('terms'));
    }


    /**
     * save Terms
     */
    public function saveTerms(Request $request)
    {
        $this->setting->set('terms', $request->terms);
        return $this->api->json(true, 'TERMS_SAVED', 'terms saved');
    }


}
