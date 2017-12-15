<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\User;

class WelcomeUser extends Mailable
{
    use Queueable, SerializesModels;


    public $user;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        $this->setting = app('App\Models\Setting');
       
        return $this->from(
            $this->setting->get('email_support_from_address'), 
            $this->setting->get('email_from_name')
        )
        ->subject('Welcome to ' . $this->setting->get('website_name'). ' :)')
        ->view('emails.welcome_user')->with([
            'user' => $this->user            
        ]);


    }
}
