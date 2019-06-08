<?php

namespace App\Repositories;

use App\Models\OtpToken;
use App\Models\Setting;
use Aloha\Twilio\Twilio;
use App\Jobs\ProcessSms;
use App\Jobs\ProcessOtp;
use App\Repositories\Utill;


class Otp 
{

	public function __construct(OtpToken $otpToken, Setting $setting)
	{
		$this->otpToken = $otpToken;
		$this->setting = $setting;
		$this->smsProvider = $this->setting->get('sms_provider')?:'twilio';
	}


	public function twilioSid()
	{
		return $this->setting->get('twilio_sid');
		//return config('twilio.twilio.connections.twilio.sid');
	}

	public function twilioToken()
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
	protected function buildMsg91JsonBody($countryCode, $mobileNo, $message)
	{
		$countryCode = ltrim($countryCode, '+');
		$body = [
			'sender' => $this->setting->get('msg91_sender_id'),
			'unicode' => '1',
			'route' => '4',
			'country' => $countryCode,
			'message' => $message,
			'mobiles' => $mobileNo,
			'authkey' => $this->setting->get('msg91_auth_key'),
		];
	
		return $body;

	}



	/**
	 * process sms from queue
	 */
	public function processSms($countryCode, $mobileNo, $message)
	{

		if($this->smsProvider == 'twilio') {

			$twilio = new Twilio($this->twilioSid(), $this->twilioToken(), $this->twilioFrom());
			$twilio->message($countryCode.$mobileNo, $message);

		}
		// send sms via msg91 
		else {

			$messageData = $this->buildMsg91JsonBody($countryCode, $mobileNo, $message);
			$queryString = http_build_query($messageData);
		
			$curl = curl_init();
			curl_setopt_array($curl, [
				CURLOPT_URL => "https://api.msg91.com/api/sendhttp.php?{$queryString}",
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => "",
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 30,
				CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
				CURLOPT_CUSTOMREQUEST => "GET",
				CURLOPT_POSTFIELDS => "",
				CURLOPT_SSL_VERIFYHOST => 0,
				CURLOPT_SSL_VERIFYPEER => 0
			]);

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
				\Log::info('Otp::processSms() error');
				\Log::info($err);
			} else {
				
				\Log::info("Otp::processSms success");
				\Log::info($response);
			}


		}

	}






    /**
     * send normal message to mobile number
     */
	public function sendMessage($countryCode, $mobileNo, $message, &$error = null)
	{
		/** push sms to job queue */
		ProcessSms::dispatch($countryCode, $mobileNo, $message);
        return true;
	}




	/**
	 * generate otp message using device type and app type and otp code
	 */
	public function generateOtpMessage($devicetype, $apptype, $otp)
	{
		return Utill::transMessage('app_messages.otp_message', [
			'otp' => $otp,
			'appname' => $this->setting->get('website_name'),
			'apphash' => Utill::appHashSms($devicetype, $apptype)
		]);
	}



	/** 
	 * returns string for msg91 otp body
	 */
	protected function buildMsg91OtpJsonBody($countryCode, $mobileNo, $message, $otpCode)
	{
		$countryCode = ltrim($countryCode, '+');
		$body = [
			'sender' => $this->setting->get('msg91_sender_id'),
			'mobile' => "{$countryCode}{$mobileNo}",
			'message' => $message,
			'authkey' => $this->setting->get('msg91_auth_key'),
			'otp' => $otpCode
		];
		
		return $body;

	}



	/**
	 * process otp
	 * takes mobile number
	 */
	public function processOtp($countryCode, $mobileNo, $message, $otpCode)
	{
		$curl = curl_init();

		$otpData = $this->buildMsg91OtpJsonBody($countryCode, $mobileNo, $message, $otpCode);
		$queryString = http_build_query($otpData);

		curl_setopt_array($curl, array(
			CURLOPT_URL => "https://control.msg91.com/api/sendotp.php?{$queryString}",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => "POST",
			CURLOPT_POSTFIELDS => "",
			CURLOPT_SSL_VERIFYHOST => 0,
			CURLOPT_SSL_VERIFYPEER => 0,
		));

		$response = curl_exec($curl);
		$err = curl_error($curl);

		curl_close($curl);

		if ($err) {
			\Log::info('Otp::processOtp() error');
			\Log::info($err);
		} else {
			
			\Log::info("Otp::processOtp success");
			\Log::info($response);
		}

	} 





    /**
     * send otp to mobile number
     */
	public function sendOTP($devicetype, $apptype, $countryCode, $mobileNo, $entityId, &$error = null)
	{
		/** delete all otp records for particular mobile number */
		$this->otpToken->where('full_mobile_number', $countryCode.$mobileNo)->delete();

		/** generate otp code */
        $otp = $this->createOtpToken($countryCode, $mobileNo);
		
		/** generate messsage */
		$message = $this->generateOtpMessage($devicetype, $apptype, $otp->token);

		/** push otp send job to queue */
		ProcessOtp::dispatch($countryCode, $mobileNo, $message, $otp->token);

        return $otp->token;
	}



	/**
	 * create otp token
	 */
	public function createOtpToken($countryCode, $mobileNo)
	{
		$otp = new $this->otpToken;
        $otp->country_code = $countryCode;
        $otp->mobile_number = $mobileNo;
        $otp->full_mobile_number = $countryCode.$mobileNo;
        $otp->token = $this->generateFourDRandomNum();
        $otp->expired_at = $this->addMinutesToTimestamp(date('Y-m-d H:i:s'), 10);
		$otp->save();
		return $otp;
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