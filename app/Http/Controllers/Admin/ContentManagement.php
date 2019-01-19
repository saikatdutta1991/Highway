<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Content;


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
        $content = Content::where('name', 'privacy_policy')->first();
        $privacy_policy = $content ? $content->content : '';
        return view('admin.contents.privacy_policy', compact('privacy_policy'));
    }


    /**
     * save privacy policy
     */
    public function savePrivacyPolicy(Request $request)
    {
        $content = Content::where('name', 'privacy_policy')->first() ?: new Content;
        $content->name = 'privacy_policy';
        $content->content = $request->privacy_policy;
        $content->save();
        return $this->api->json(true, 'PRIVACY_POLICY_SAVED', 'Privacy policy saved');
    }


    /**
     * show Terms management
     */
    public function showTerms()
    {
        $content = Content::where('name', 'terms')->first();
        $terms = $content ? $content->content : '';
        return view('admin.contents.terms', compact('terms'));
    }


    /**
     * save Terms
     */
    public function saveTerms(Request $request)
    {
        $content = Content::where('name', 'terms')->first() ?: new Content;
        $content->name = 'terms';
        $content->content = $request->terms;
        $content->save();
        return $this->api->json(true, 'TERMS_SAVED', 'terms saved');
    }


}
