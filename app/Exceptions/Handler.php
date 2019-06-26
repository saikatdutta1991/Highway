<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Route;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        //if request uri contains /api/v1/ and app debug mode false
//         if (strpos($request->getRequestUri(), '/api/v1/') !== false && !env('APP_DEBUG')) {
//             return app('App\Repositories\Api')->json(false, 'Api not found');
//         }


        /** if not in debug mode */
        if(!env('APP_DEBUG')) {
            $header = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : 'Somethiing Wrong Happened';
        
            return response()->view('error', [
                    'title' => 'Something Happend', 
                    'header' => $header, 
                    'message' => $this->getHtmlMessage()
                ]);
        }

        return parent::render($request, $exception);       
        
    }

    /** get message string */
    protected function getHtmlMessage()
    {
        return <<<HTML
        <h3>Hmm, We are having trouble finding the site.</h3>
        <p>We can't connect to the server.</p>
        <b>If that address is correct, here three other things you can try:</b>
        <ul>
            <li>Try agiain later.</li>
            <li>Check your network connection.</li>
            <li>If you are connected but behind a firewall, check that Nightly has permission to access the web.</li>
        </ul>
HTML;
    }


}
