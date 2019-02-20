<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class ProcessCurlPost implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    /** instance properties */
    protected $url;
    protected $headers;
    protected $fields;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $headers, $fields)
    {
        $this->url = $url;
        $this->headers = $headers;
        $this->fields = $fields;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Log::info("Process curl post \n");
        
		try {


            \Log::info('fields');
            \Log::info($this->fields);

			$ch = curl_init();
		    curl_setopt($ch, CURLOPT_URL, $this->url);
		    curl_setopt($ch, CURLOPT_POST, true);
		    curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		    curl_setopt($ch, CURLOPT_POSTFIELDS, $this->fields);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		    /* if($this->isIPv4Resolve) {
		    	curl_setopt ($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
		    } */
		    $result = curl_exec($ch);
		    
		    if(curl_errno($ch)){
		    	throw New \Exception(curl_error($ch), curl_errno($ch));
			}
			curl_close($ch);
            
            \Log::info('url : '. $this->url);
            \Log::info($result);

		} catch(\Exception $e) {
			\Log::info($e->getMessage());
        }
        

    }
}
