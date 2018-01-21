<?php

namespace App\Repositories;

/**
 * This class used to retrive the instance of payment gateway class generic
 */

abstract class Gateway
{

	protected static $gatewayClassMaps = [
		'razorpay' => 'App\\Repositories\\RazorPay',
	];

    abstract public function gatewayName();
    abstract public function publickeys();
    abstract public function allKeys();
	abstract public function charge($request);
	
	
	public static function instance($gName)
	{
		return isset(self::$gatewayClassMaps[strtolower($gName)]) ? app(self::$gatewayClassMaps[strtolower($gName)]) : null;
	} 
}