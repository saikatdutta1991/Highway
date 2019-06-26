<?php

namespace App\Http\Middleware;

use Closure;

class CORS
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
       \Log::info('cors'); 
        $headers = [
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'Allow, POST, GET, OPTIONS, PUT, DELETE',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, Accept, Authorization, X-Request-With',
            'Access-Control-Allow-Credentials' => 'true'
        ];
        
//         if($request->getMethod() == "OPTIONS") {
//             $response = response("OK", 200);
//             foreach($headers as $key => $value)
//                 $response->header($key, $value);
//            return $response;
//         }
        

        $response = $next($request);
        foreach($headers as $key => $value)
            $response->header($key, $value);
            
        return $response;
    }
}
