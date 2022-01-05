<?php

namespace Services\Cors;

/**
 * CORS handler:
 * if request is made by a web browser the cors headers must be return
 * in response of a "preflight" of the origin (an OPTIONS request)
 */
class Cors
{
    public static function handle(string $endpoint)
    {
        if (isset($_SERVER['HTTP_ORIGIN']) && $_SERVER['HTTP_ORIGIN'] != '') {
            $allowedOrigins = [
                '(http(s)://)?localhost:3000',
                '(http(s)://)?bank.com',
            ];

            $allowedMethods = [
                'auth' => 'GET',
                'deposit' => 'POST, GET, DELETE, PUT',
                'exit' => 'DELETE',
                'history' => 'GET',
                'user' => 'POST, GET, DELETE'
            ];

            $allowedHeaders = [
                'auth' => 'username, password',
                'deposit' => 'token, name, type, amount, destination, action',
                'exit' => 'token',
                'history' => 'token, name',
                'user' => 'username, password, name, surname, token'
            ];

            foreach ($allowedOrigins as $allowedOrigin) {
                if (preg_match('#' . $allowedOrigin . '#', $_SERVER['HTTP_ORIGIN'])) {
                    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
                    header('Access-Control-Allow-Methods: OPTIONS, ' . $allowedMethods[$endpoint]);
                    header('Access-Control-Max-Age: 1000');
                    header('Access-Control-Allow-Headers: ' . $allowedHeaders[$endpoint]);
                    break;
                }
            }
        }
    }
}
