<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Driver;

class ProcessDriverRating implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $driverId;
    
    public function __construct($driverId)
    {
        $this->driverId = $driverId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info('calculate and savign driver rating');
        
        /** calculate driver rating */
        $rating = Driver::calculateRating($this->driverId);
        \Log::info('calculated rating : ' . $rating);

        /** update driver rating */
        Driver::where('id', $this->driverId)->update(['rating' => $rating]);

    }
}
