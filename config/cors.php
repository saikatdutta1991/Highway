<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Laravel CORS
    |--------------------------------------------------------------------------
    |
    | allowedOrigins, allowedHeaders and allowedMethods can be set to array('*')
    | to accept any value.
    |
    */
   
    'supportsCredentials' => true,
    'allowedOrigins' => ['*'],
    'allowedOriginsPatterns' => [],
    'allowedHeaders' => ['Origin, Content-Type, Accept, Authorization, X-Request-With, cache-control,postman-token, token'],
    'allowedMethods' => ['*'],
    'exposedHeaders' => ['Origin, Content-Type, Accept, Authorization, X-Request-With, cache-control,postman-token, token'],
    'maxAge' => 0,

];
