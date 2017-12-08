<?php

namespace App\Repositories;

use App\Models\OtpToken;
use App\Models\Setting;
use Aloha\Twilio\Twilio;


class Otp 
{

	public function __construct(OtpToken $otpToken, Setting $setting)
	{
		$this->otpToken = $otpToken;
		$this->setting = $setting;
		$this->smsProvider = $this->setting->get('sms_provider')?:'twilio';
	}


	protected function twilioSid()
	{
		return $this->setting->get('twilio_sid');
		//return config('twilio.twilio.connections.twilio.sid');
	}

	protected function twilioToken()
	{
		return $this->setting->get('twilio_token');
		//return config('twilio.twilio.connections.twilio.token');
	}

	public function twilioFrom()
	{
		return $this->setting->get('twilio_from');
		//return config('twilio.twilio.connections.twilio.from');
	}



	/**
	 *  returns msg91 send sms api jsonbody
	 */
	protected function buildMsg91JsonBody($countryCode, $mobileNo, $msgText)
	{
		$countryCode = ltrim($countryCode, '+');
		$body = [
			'sender' => $this->setting->get('msg91_sender_id'),
			'route' => '4',
			'country' => $countryCode,
			'sms' => [
				[
					'message' => $msgText,
					'to' => [$mobileNo]
				]
			]
		];
		
		\Log::info('MSG91_BODY_DATA');
		\Log::info($body);
		
		return json_encode($body);

	}


    /**
     * send normal message to mobile number
     */
	public function sendMessage($countryCode, $mobileNo, $message, &$error = null)
	{
		try{

			if($this->smsProvider == 'twilio') {

				$twilio = new Twilio($this->twilioSid(), $this->twilioToken(), $this->twilioFrom());
				$twilio->message($countryCode.$mobileNo, $message);

			}
			// send sms via msg91 
			else {

				$curl = curl_init();

				curl_setopt_array($curl, [
  					CURLOPT_URL => "http://api.msg91.com/api/v2/sendsms",
  					CURLOPT_RETURNTRANSFER => true,
  					CURLOPT_ENCODING => "",
  					CURLOPT_MAXREDIRS => 10,
  					CURLOPT_TIMEOUT => 30,
  					CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  					CURLOPT_CUSTOMREQUEST => "POST",
  					CURLOPT_POSTFIELDS => $this->buildMsg91JsonBody($countryCode, $mobileNo, $message),
  					CURLOPT_SSL_VERIFYHOST => 0,
  					CURLOPT_SSL_VERIFYPEER => 0,
  					CURLOPT_HTTPHEADER => [
						"authkey: ".$this->setting->get('msg91_auth_key'),
						"content-type: application/json"
					],
				]);

				$response = curl_exec($curl);
				$err = curl_error($curl);

				curl_close($curl);

				if ($err) {
					throw new \Exception($error);
				} else {
					
					\Log::info("SEND MESSAGE msg91 response");
					\Log::info($response);

					$response = json_decode($response);
					 
					if($response->type == 'error') {
						throw new \Exception($response->message, $response->code);
					} 
				}


			}


			
      	} catch(\Exception $e){
			 $error = $e->getMessage();
			 \Log::info("SEND MESSAGE");
			 \Log::info($e->getMessage());
             return false;
        }

        return true;
	}



    /**
     * send otp to mobile number
     */
	public function sendOTP($countryCode, $mobileNo, $message, $entityId, &$error = null)
	{

		$this->otpToken->where('full_mobile_number', $countryCode.$mobileNo)->delete();

        $otp = new $this->otpToken;
        
        $otp->country_code = $countryCode;
        $otp->mobile_number = $mobileNo;
        $otp->full_mobile_number = $countryCode.$mobileNo;
        $otp->token = $this->generateFourDRandomNum();
        $otp->expired_at = $this->addMinutesToTimestamp(date('Y-m-d H:i:s'), 10);
        $otp->save();

        $message = str_replace("{{otp_code}}", $otp->token, $message);

        $ismsgsent = $this->sendMessage($countryCode, $mobileNo, $message, $error);

        return $ismsgsent ? $otp->token : false;
	}


    /**
     * returns 4 digit random number
     */
	public function generateFourDRandomNum()
   	{
   		return rand(1000, 9999);
   	}




    /**
     * add minutes to datetime (d-m-y H:i:s)
     */
    public function addMinutesToTimestamp($timestamp, $min)
    {
        return date('Y-m-d H:i:s', strtotime('+ '.$min.' minutes', strtotime($timestamp) ));
    }





	/**
	 * check is otp expired
	 */
	protected function otpExpired($startTimeString, $maxMin)
	{
		return !$this->timeMinuteAgo($startTimeString, $maxMin);
	}


	

	/**
	 * check a given timestamp is brefore(ago) than given minute
	 */
	public function timeMinuteAgo($timeStamp, $minuteAgo) 
	{
   		$to_time = gmdate("Y-m-d H:i:s", time());
        $minute = $this->timeDiffMinutes($timeStamp, $to_time);
        return ($minute <= $minuteAgo) ? true : false;
   	}


	
	/**
	 * find time differennce in minutes
	 */
	public function timeDiffMinutes($startTimeStamp, $endTimestamp)
   	{
   		$from_time = strtotime($startTimeStamp);
        $to_time = strtotime($endTimestamp);
       	return round(($to_time - $from_time) / 60);
   	}



	/**
	 * substract minute from timestamp
	 */
	public function subMinutesToTimestamp($timestamp, $min)
    {
        return date('Y-m-d H:i:s', strtotime('- '.$min.' minutes', strtotime($timestamp) ));
    }



	/**
	 * verify otp
	 */
	public function verifyOTP($countryCode, $mobileNo, $otpCode)
	{
		$otp = $this->otpToken
		->where('mobile_number', $mobileNo)
		->where('country_code', $countryCode)
        ->where('token', $otpCode)
    	->select(['id', 'expired_at'])
        ->first();
		
        if(!$otp) { 
        	return false; 
       	}

       	$success = !$this->otpExpired($otp->expired_at, 10);

       	if($success) {
			$otp->expired_at = $this->subMinutesToTimestamp(date('Y-m-d H:i:s'), 60);
			$otp->save();
       		$otp->delete();
       	}

        return $success;
	}





    /* 

    public function msgReplace($data = [], $msg)
    {
        foreach($data as $key => $value) {
            $msg = str_replace($key, $value, $msg); 
        }
        return $msg;
	} */
	


}