<?php

namespace App\Repositories;


/**
* Api class
* 
* This class is part of Highway product. 
* Used for token based authentication methods and tokens api response base structure
*
* @author  saikat
*/

use App\Models\AccessToken;
use App\Models\User;
use App\Models\Driver;

class Api
{

	/**
	* initialize the the dependencies
	*
	* @param object $user  User model
	* @param object $accessToken  AccessToken model
	*/
	public function __construct(AccessToken $accessToken, User $user, Driver $driver)
	{
		$this->accessToken = $accessToken;
		$this->user = $user;
		$this->driver = $driver;
	}




	/**
	 * unauthorised response
	 */
	public function unAuthrizedResponse()
	{
		return response()->json(
			$this->createResponse(false, 'UNAUTHORIZED', 'Access unauthorized')
		);
	}






	/**
	* return json response for unknown error
	*
	* @return return json response object string with prefilled for unknown error response
	*/
	public function unknownErrResponse($data = [])
	{
		return response()->json(
			$this->createResponse(false, 'UNKNOWN_ERROR', 'Server error. Try after sometime.', $data)
		);
	}



	/**
	* used to create response array structure for all api responses
	*
	* @param boolean $success (if api call success then true or false)
	* @param stirng $type type of the api response
	* @param stirng $text text of the api response
	* @param mixed $data default empty array
	* @return array
	*/
	public function createResponse($success = true, $type = "", $text = "", $data = [])
	{
		return [
			'success' => $success,
			'type' => $type,
			'text' => $text,
			'data' => $data
		];
	}



    /**
     * this returns laravel response json object
     */
	public function json($success = true, $type = "", $text = "", $data = [])
	{
		return response()->json($this->createResponse($success, $type, $text, $data));
	}





	/**
	* parse and extract access token from Authorization header value
	*
	* @param stirng $authorizationHeader  authorization token header value
	* @return string authentication token
	*/
	public function getAccessToken($authorizationHeader)
	{
		return str_replace('Bearer ', "", $authorizationHeader);
	}


	/**
	* creates random string (used for access token)
	*
	* @return string random string
	*/
	public function createAccessToken($prefix)
	{
		return $prefix.md5(uniqid(mt_rand(), true)).md5(uniqid(mt_rand(), true));
	}




	/**
	 * save access token
	 */
	public function saveAccessToken($eId, $etype)
	{
		//$at = $this->accessToken->where('entity_id', $eId)->where('entity_type', $etype)->first() ?: new $this->accessToken;
		$at = new $this->accessToken;
		$at->entity_id = $eId;
		$at->entity_type = strtoupper($etype);
		$at->access_token = $this->createAccessToken($eId.'_');
		$at->save();
		return $at;

	}



	/** 
	 * remove access token from db
	 */
	public static function removeAccessToken($entityid, $entitytype, $accesstoken)
	{
		$query = AccessToken::where('entity_id', $entityid)->where('entity_type', $entitytype);

		if($accesstoken) {
			$query = $query->where('access_token', $accesstoken);
		}

		$query->forceDelete();

		return true;
	}








	/**
	 * find entitiy(user or driver) by access token
	 */
	public function shouldPassThrough($eType, $token)
	{
		$eType = strtoupper($eType);

		switch ($eType) {

			case 'USER':

				return $this->user->join(
					$this->accessToken->getTableName(), 
					$this->user->getTableName().'.id', 
					'=', 
					$this->accessToken->getTableName().'.entity_id'
				)
				->where('entity_type', $eType)
				->where('access_token', $token)
				->select($this->user->getTableName().'.*')
				->first();

				break;

			case 'DRIVER':

				return $this->driver->join(
					$this->accessToken->getTableName(), 
					$this->driver->getTableName().'.id', 
					'=', 
					$this->accessToken->getTableName().'.entity_id'
				)
				->where('entity_type', $eType)
				->where('access_token', $token)
				->select($this->driver->getTableName().'.*')
				->first();
				break;
		
		}

		return false;
		
	}



 


	/**
	 * store log info with tag
	 */
	public static function Log($tag, $data)
	{
		\Log::info(strtoupper($tag));
		\Log::info($data);
	}




}