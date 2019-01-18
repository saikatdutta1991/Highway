<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Promotion as PromotionModel;
use Validator;
use DB;
use View;


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
     * preview email 
     */
    public function previewPromotionEmail(Request $request)
    {
        $promotion = PromotionModel::find($request->promotion_id);
        
        View::addNamespace('EMAIL', public_path("promotions/email_contents"));
        $filename = basename($promotion->email_file, '.blade.php');        
    
        return View::make("EMAIL::{$filename}");
    }




    /**
     * delete api
     */
    public function deletePromotion(Request $request)
    {        
        $promotion = PromotionModel::find($request->promotion_id);
        $promotion->forceDelete();
        if($promotion->has_email) {
            unlink($promotion->email_file);
        }

        return $this->api->json(true, 'PROMOTION_DELETED', 'Promotion deleted successfully');
    }




    /**
     * save promotion
     */
    public function savePromotion(Request $request)
    {
        /** validate input parms based on new or update */
        $validator = Validator::make($request->all(), $request->has('id') ? [
            'title' => 'required|max:128|unique:promotions,title,'.$request->id,
            'broadcast_type' => 'required',
            'has_pushnotification' => 'required|boolean',
            'pushnotification_title' => 'max:256',
            'pushnotification_message' => 'max:500',
            'has_email' => 'required|boolean',
            'email_content' => '',
            'email_subject' => 'max:256',
        ] : [
            'title' => 'required|max:128|unique:promotions,title',
            'broadcast_type' => 'required',
            'has_pushnotification' => 'required|boolean',
            'pushnotification_title' => 'max:256',
            'pushnotification_message' => 'max:500',
            'has_email' => 'required|boolean',
            'email_content' => '',
            'email_subject' => 'max:256'
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
        
        
        try {
            
            DB::beginTransaction();
            
            $promotion->save();

            /** write email content to file if has_email */
            if($promotion->has_email) {

                /** generate file name */
                $filename = "promotion_{$promotion->id}.blade.php";
                
                /** generate file with path */
                $file = public_path("promotions/email_contents/{$filename}");

                app('UtillRepo')->writeFile($file, $promotion->email_content);

                /** save the file location in db */
                $promotion->email_file = $file;
                $promotion->save();

            }

       
            DB::commit();
            

        } catch(\Exception $e) {
            DB::rollback();
            return $this->api->json(false, 'PROMOTION_FAILED', $e->getMessage());
        }


        return $this->api->json(true, 'PROMOTION_SAVED', 'Promotion saved successfully');

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
     * show edit promotion
     */
    public function showEditPromotion(Request $request)
    {
        $promotion = PromotionModel::find($request->promotion_id);
        return view('admin.promotions.add_promotion', compact('promotion'));
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
