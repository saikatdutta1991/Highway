<?php

namespace App\Http\Middleware;

use Closure;
use App\Repositories\Api;

class UserApiAuth
{


    public function __construct(Api $api)
    {
        $this->api = $api;
    }


    /**
     * filter the user api unauthorized access
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $accessToken = $this->api->getAccessToken($request->header('Authorization'));

        if($user = $this->api->shouldPassThrough('user', $accessToken)) {

            //adding auth_user with $request
            $request->request->add(['auth_user' => $user]);
            return $next($request);
        }

        return $this->api->unAuthrizedResponse();

    }


}
