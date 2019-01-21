<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Promotion;

class PromotionEmail extends Mailable
{
    use Queueable, SerializesModels;

    
    public $viewname;
    public $subject;
    
    public function __construct($viewname, $subject)
    {
        $this->viewname = $viewname;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        Promotion::loadEmailViewPath();
        $this->setting = app('App\Models\Setting');
       
        return $this->subject($this->subject)
        ->from(
            $this->setting->get('email_support_from_address'), 
            $this->setting->get('email_from_name')
        )
        ->view($this->viewname);
        
    }
}
