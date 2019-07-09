<?php
namespace App\Repositories;
/**
* PushNotificationRepository classs
* 
* This class is part of FirebaseCloudMessaging package. Can be used to send push notifications.
* This library needs php-curl extension
* If someone using laravel then can set config.firebase for server key and push url
*
* @package    FirebaseCloudMessaging
* @author     SAIKAT DUTTA <saikatdutta1991@gmail.com>
*/

use App\Jobs\ProcessCurlPost;
use App\Models\Setting;

class PushNotification
{

	/**
	 * push notification device types
	 */
	const ANDROID = 'ANDROID';
	const IOS = 'IOS';


	/**
	* firebase server to autenticate and to send 
	*/
	protected $serverKey = "";
	/**
	* firebase server url
	*/
	protected $fcmURL = "https://fcm.googleapis.com/fcm/send";
	/**
	* curl ipv4 address resolve
	*/
	protected $isIPv4Resolve = false;
	
	/**
	* firebase push notification title
	*/
	protected $notifTitle = "";

	/**
	* firebase push notification body
	*/
	protected $notifBody = "";

	/**
	* firebase push notification icon
	*/
	protected $notifIcon = "";

	/**
	* firebase push notification click action
	*/
	protected $notifClickAction = "";

	/**
	* firebase push notification click action
	*/
	protected $notifCustomPayload = [];

	/**
	* firebase push notification device tokens
	*/
	protected $deviceTokens = [];

	/**
	* firebase push notification respnose raw string
	*/
	const RAW = 1;

	/**
	* firebase push notification respnose php standard class object type
	*/
	const STDCLASS = 2;

	/**
	* firebase push notification respnose php array type
	*/
	const ARRY = 3;

	/**
	* firebase push notification priority constants
	*/
	const HIGH = 'high';
	const LOW = 'low';

	/**
	* firebase push notification priority
	*/
	protected $priority;
	
	/**
	* firebase push notification option content availabe
	*/
	protected $content_available = true;
	/**
   * initialize the sender id and server key
   *
   * @param string $senderID  fcm sender messaging id
   * @param string $serverKey  fcm server key to send authentication
   * @param string $fcmURL  fcm pushnotification url
   */
	public function __construct()
	{
		$this->serverKey = Setting::get('firebase_cloud_messaging_server_key');
		$this->priority = PushNotification::HIGH;
	}

	
	public function setPriority($priority)
	{
		$this->priority = $priority;
		return $this;
	}

	public function setContentAvailable($bool = false)
	{
		$this->content_available = $bool;
		return $this;
	}


	/** 
	 * set device tokens to send push
	 * convert device tokens as array always
	 */
	public function setDeviceTokens($tokens, $merge = true)
	{
		/** make tokens array, if string also */
		$tokens = is_string($tokens) ? [$tokens] : $tokens;

		/** need to merge with previous tokens */
		$this->deviceTokens = $merge ? array_merge($this->deviceTokens, $tokens) : $tokens;
		
		return $this;
	}


	/** 
	 * set notification title
	 */
	public function setTitle($title = "")
	{
		$this->notifTitle = $title;
		return $this;
	}

	/**
	 * set notification message body
	 */
	public function setBody($body = "")
	{
		$this->notifBody = $body;
		return $this;
	}

	/** 
	 * set notification icon
	 */
	public function setIcon($icon = "")
	{
		$this->notifIcon = $icon;
		return $this;
	}

	/** 
	 * set notification click action
	 */
	public function setClickAction($actionUrl = "")
	{
		$this->notifClickAction = $actionUrl;
		return $this;
	}

	/** 
	 * set custom payload
	 * custom payload must be an associate array with values
	 */
	public function setCustomPayload($payload)
	{
		$this->notifCustomPayload = is_array($payload) ? $payload : [];
		return $this;
	}


	public function setIPv4Resolve($bool)
	{
		$this->isIPv4Resolve = $bool;
		return $this;
	}

	
	/**
   * 
   * Post data to a url and return response
   *
   */
	public function postURL($url, $headers, $fields)
	{		
		ProcessCurlPost::dispatch($url, $headers, $fields);
		return '{"message" : "Pushed to queue"}';
	}


	protected function buildNotification()
	{
		$notiffication = [];
		$notification['title'] = $this->notifTitle;
		$notification['body'] = $this->notifBody;
		$notification['icon'] = $this->notifIcon;
		$notification['click_action'] = $this->notifClickAction;

		return array_filter($notification);
	}

	/** 
	 * sends push message to firebase server via curl..
	 * laravel job queue is used to send this post request
	 */
	public function push()
	{
		/** set params for firebase */
		$params = [
			"registration_ids" => $this->deviceTokens,
			"priority" => $this->priority,
			'content_available' => $this->content_available
		];

		
		/** set data payload if not empty */
		if(!empty($this->notifCustomPayload)) {
			$params['data'] = $this->notifCustomPayload;
		}

		/** set notification if not empty */
		$notification = $this->buildNotification();
		if(!empty($notification)) {
			$params['data']['notification'] = $notification;
		}
	
	    $fields = json_encode($params);
	    $headers = [
	        'Authorization: key=' . $this->serverKey,
	        'Content-Type: application/json'
	    ];
		$response = $this->postURL($this->fcmURL, $headers, $fields);

		return $response;		
	}
	


	/**
	 * returns device types
	 */
	public static function deviceTypes()
	{
		return [self::ANDROID, self::IOS];
	}
    
    
}