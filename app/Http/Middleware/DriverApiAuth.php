<?php

namespace App\Http\Middleware;

use Closure;
use App\Repositories\Api;

class DriverApiAuth
{


    public function __construct(Api $api)
    {
        $this->api = $api;
    }


    /**
     * filter the driver api unauthorized access
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $accessToken = $this->api->getAccessToken($request->header('Authorization'));

        if($driver = $this->api->shouldPassThrough('driver', $accessToken)) {

            //adding auth_user with $request
            $request->request->add(['auth_driver' => $driver, 'access_token' => $accessToken]);
            return $next($request);
        }
        return $this->api->unAuthrizedResponse();

    }


}
