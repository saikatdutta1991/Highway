<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommonTemplate extends Mailable
{
    use Queueable, SerializesModels;

    public $body;
    public $subject;
    public $welcomename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    
    public function __construct($welcomename, $subject, $body)
    {
        $this->welcomename = $welcomename;
        $this->subject = $subject;
        $this->body = $body;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->setting = app('App\Models\Setting');
        $this->utillRepo = app('UtillRepo');
       
        return $this->from(
            $this->setting->get('email_support_from_address'), 
            $this->setting->get('email_from_name')
        )
        ->subject($this->subject)
        ->view('emails.common')->with([
            'subject' => $this->subject,
            'body' => $this->body,
            'welcomename' => $this->welcomename
        ]);
        
    }
}
