<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessOtp implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /** instance variables */
    protected $countryCode;
    protected $mobileNo;
    protected $message;
    protected $smsRepo;
    protected $otpCode;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($countryCode, $mobileNo, $message, $otpCode)
    {
        $this->countryCode = $countryCode;
        $this->mobileNo = $mobileNo;
        $this->message = $message;
        $this->otpCode = $otpCode;
        $this->smsRepo = app('App\Repositories\Otp');
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->smsRepo->processOtp($this->countryCode, $this->mobileNo, $this->message, $this->otpCode);
    }
}
