<?php

namespace App\Http\Controllers\Apis\User;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Support\UserTicket as Ticket;
use Validator;
use App\Repositories\Utill;
use App\Models\Setting;
use Mail;
use App\Mail\CommonTemplate;


class Support extends Controller
{

    /**
     * init dependencies
     */
    public function __construct(Api $api)
    {
        $this->api = $api;
    }


    /**
     * get all user raised tickets
     */
    public function getTickets(Request $request)
    {
        $tickets = Ticket::where('user_id', $request->auth_user->id)->orderBy('updated_at', 'desc')->get();
        return $this->api->json(true, 'TICKETS', 'All tickets', $tickets);
    }



    /**
     * creates new ticket 
     */
    public function createTicket(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'type' => 'required|max:256',
            'description' => 'sometimes|required',
            'photo1' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'photo2' => 'sometimes|required|image|mimes:jpg,jpeg,png',
            'photo3' => 'sometimes|required|image|mimes:jpg,jpeg,png',
        ]);

        if($validator->fails()) {

            $e = $validator->errors();
            $msg = [];
            ($e->has('type')) ? $msg['type'] = $e->get('type')[0] : '';
            ($e->has('description')) ? $msg['description'] = $e->get('description')[0] : '';
            ($e->has('photo1')) ? $msg['photo1'] = $e->get('photo1')[0] : '';
            ($e->has('photo2')) ? $msg['photo2'] = $e->get('photo2')[0] : '';
            ($e->has('photo3')) ? $msg['photo3'] = $e->get('photo3')[0] : '';

            return $this->api->json(false, 'VALIDATION_ERROR', 'Enter all the mandatory fields', $msg);

        }


        /** create new ticket */
        $ticket = new Ticket;
        $ticket->user_id = $request->auth_user->id;
        $ticket->number = Utill::randomChars(10);
        $ticket->type = ucwords($request->type);
        $ticket->description = $request->has('description') ? $request->description : '';
        $ticket->status = Ticket::PENDING;
        
        if($request->has('photo1')) {
            $ticket->photo1 = Ticket::savePhoto($request->photo1);
        }

        if($request->has('photo2')) {
            $ticket->photo2 = Ticket::savePhoto($request->photo2);
        }

        if($request->has('photo3')) {
            $ticket->photo3 = Ticket::savePhoto($request->photo3);
        }

        $ticket->save();


        /** send mail to support admins */
        $mailids = explode(',', Setting::get('support_ticket_notify_emails'));
        $body = "User {$request->auth_user->email} has raised new support ticket. Check out from admin panel.";
        $resCode = Mail::to($mailids)->queue( new CommonTemplate('Admin', 'New Support Ticket Raised', $body) );
        \Log::info('MAIL PUSHED TO QUEUE, RESCODE :' . $resCode);


        return $this->api->json(true, 'TICKET_RAISED', 'Ticket raised successfully');

    }



}
