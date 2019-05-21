<?php

namespace App\Http\Controllers\Admin;

use App\Repositories\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Validator;
use App\Models\Support\UserTicket;
use App\Models\Support\DriverTicket;


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
     * show user tickets
     */
    public function getUserTickets(Request $request)
    {

        $ticketsCount = UserTicket::count();
        $pendingTickets = UserTicket::where('status', UserTicket::PENDING)->count();
        $processingTickets = UserTicket::where('status', UserTicket::PROCESSING)->count();
        $resolvedTickets = UserTicket::where('status', UserTicket::RESOLVED)->count();


        $tickets = UserTicket::with('user')
            ->orderByRaw("FIELD(status , '".UserTicket::PROCESSING."', '".UserTicket::PENDING."', '".UserTicket::RESOLVED."') ASC")
            ->orderBy('created_at', 'desc')
            ->paginate(1000);


        return view('admin.support.user_tickets', [
            'tickets' => $tickets,
            'ticketsCount' => $ticketsCount,
            'pendingTickets' => $pendingTickets,
            'resolvedTickets' => $resolvedTickets,
            'processingTickets' => $processingTickets
        ]);
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
