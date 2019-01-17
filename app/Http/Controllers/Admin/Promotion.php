<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class Promotion extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }


    /**
     * show view for add new promotion
     */
    public function showAddPromotion()
    {
        return view('admin.promotions.add_promotion');
    }


}
