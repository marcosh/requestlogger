<?php

use Phly\Http\ServerRequestFactory;
use Phly\Http\Response;
use Phly\Http\Server;

chdir(dirname(__DIR__));

// Setup autoloading
include 'vendor/autoload.php';

//create request instance from superglobals
$request = ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

//create response instance
$response = new Response();

//serving the application
$server = new Server(
    function ($request, $response, $done) {
        $messageLine = 'MESSAGE LINE: '.
            $request->getMethod().' '.
            $request->getRequestTarget().' '.
            'HTTP/'.$request->getProtocolVersion().PHP_EOL;

        $headers = 'HEADERS: '.PHP_EOL;
        foreach ($request->getHeaders() as $name => $values) {
            $headers .= $name.': '.implode(', ', $values).PHP_EOL;
        }
        $headers .= PHP_EOL;

        $body = $request->getBody();

        $data = $messageLine.$headers.PHP_EOL.$body.PHP_EOL.PHP_EOL;

        file_put_contents("requests.log", $data, FILE_APPEND);
    },
    $request,
    $response
);

//listen to incomping requests
$server->listen();
