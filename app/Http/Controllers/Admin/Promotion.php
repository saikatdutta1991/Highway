<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion as PromotionModel;
use Validator;


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
     * save promotion
     */
    public function savePromotion(Request $request)
    {
        /** validate input parms based on new or update */
        $validator = Validator::make($request->all(), $request->has('id') ? [
        ] : [
            'title' => 'required|max:128|unique:promotions,title',
            'broadcast_type' => 'required',
            'has_pushnotification' => 'required|boolean',
            'pushnotification_title' => 'max:256',
            'pushnotification_message' => 'max:500',
            'has_email' => 'required|boolean',
            'email_content' => '',
            'email_subject' => 'max:256',
        ]);

        if($validator->fails()) {

            $messages = [];
            foreach($validator->errors()->getMessages() as $attr => $errArray) {
                $messages[$attr] = $errArray[0];
            }
            
            return $this->api->json(false, 'VALIDATION_ERROR', current($messages), $messages);
        }

        $promotion = $request->has('id') ? PromotionModel::find($request->id) : new PromotionModel;
        $promotion->title = $request->title;
        $promotion->broadcast_type = $request->broadcast_type;
        $promotion->has_pushnotification = $request->has_pushnotification;
        $promotion->pushnotification_title = $request->pushnotification_title ?: '';
        $promotion->pushnotification_message = $request->pushnotification_message ?: '';
        $promotion->has_email = $request->has_email;
        $promotion->email_content = $request->email_content ?: '';
        $promotion->email_subject = $request->email_subject ?: '';
        $promotion->status = (!$request->has('id')) ? PromotionModel::SCREATED : $promotion->status;
        $promotion->save();

        return $this->api->json(true, 'PROMOTION_CREATED', 'Promotion created successfully');

    }




    /**
     * get sample email template
     */
    public function getSampleEmailTemplate()
    {
        return view('admin.promotions.sample_email_template');
    }




    /**
     * show view for add new promotion
     */
    public function showAddPromotion()
    {
        return view('admin.promotions.add_promotion');
    }



    /**
     * show promotions list
     */
    public function showPromotions()
    {
        $promotions = PromotionModel::orderBy('created_at', 'desc')->get();
        return view('admin.promotions.promotions', [
            'promotions' => $promotions
        ]);
    }


}
