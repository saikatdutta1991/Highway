<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Promotion;
use App\Models\User;
use App\Models\DeviceToken;
use App\Repositories\PushNotification;
use App\Jobs\ProcessSms;
use Mail;

class PromotionBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $promotion;
    public $pushHelper;
    public $setting;
    
    public function __construct(Promotion $promotion)
    {
        $this->promotion = $promotion;
        $this->pushHelper = new PushNotification;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        /** now promotion can be broadcasted to only users, so broadcast type checking ignored */


        /** 
         * inititialie push helper push notificaiton title and message 
         * because title and message wont change 
         */
        $this->pushHelper->setTitle($this->promotion->pushnotification_title)
            ->setBody($this->promotion->pushnotification_message)
            ->setIcon('logo')
            ->setClickAction('')
            ->setCustomPayload([])
            ->setPriority(PushNotification::HIGH)
            ->setContentAvailable(true); 


        /** laod email view path */
        Promotion::loadEmailViewPath();
        $this->setting = app('App\Models\Setting');



        /** change promotion status to processing */
        $this->promotion->status = Promotion::SPROCESSING;
        $this->promotion->save();


        try {

            $this->broadcast(); //broacast process

        } catch(\Exception $e) {
            /** if any error happends change promotion status to created */
            $this->promotion->status = Promotion::SCREATED;
            $this->promotion->save();
            

        }


        /** change promotion status to processed */
        $this->promotion->status = Promotion::SPROCESSED;
        $this->promotion->save();


    }


    /**
     * process broadcast
     */
    protected function broadcast()
    {
        /** fetch user emails, mobile and device tokens */
        $users = User::select(['id', 'email', 'country_code', 'mobile_number'])
        ->with('deviceTokens:device_token,entity_id')
        ->chunk(100, function($users) use(&$count) {


            /** check protmotion has push notification, the send */
            if($this->promotion->has_pushnotification) {

                /** fetch device tokens */
                $deviceTokens = $users->pluck('deviceTokens.*.device_token')->flatten()->toArray();

                //send pushnotifications
                $this->pushHelper->setDeviceTokens($deviceTokens, false)->push();
                
            }


            /** check promotion has email, then send emails */
            if($this->promotion->has_email) {

                /** fetch email ids */
                $emailids = $users->pluck('email')->toArray();

                //send email goes here
                Mail::send($this->promotion->getEmailViewName(), [], function ($message) use($emailids) {
                    $message->subject($this->promotion->email_subject)
                        ->from(
                            $this->setting->get('email_support_from_address'), 
                            $this->setting->get('email_from_name')
                        )
                        ->to($emailids);
                });

            }


            /** check promotion has sms then send sms each users */
            foreach($users as $user) {
                ProcessSms::dispatch($user->country_code, $user->mobile_number, $this->promotion->sms_text);
            }




        });

        


    }


}
