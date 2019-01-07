<?php

namespace App\Http\Controllers\Api;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use App\Http\Controllers\Controller;

class UserController extends Controller
{
    public function me () {


        $response = null;
        try {
            $http = new Client;
            $app = \App\App::first();
            if ( ! $app) {
                throw new Exception('Esta aplicación no está autorizada para consumir la API', 401);
            }

            $response = $http->request('GET', 'http://passport-api.test/api/user', [
                'verify' => false,
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Bearer ' . $app->access_token,
                ],
            ]);
        } catch (GuzzleException $exception) {
            if ($exception->getCode() === 401) {

                return redirect('/refresh-token');
            }
        }

        return json_decode((string) $response->getBody(), true);
    }
}
