<?php

namespace App\Http\Controllers\Api;

use App\App;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use GuzzleHttp\Exception\ClientException;

class AuthenticationController extends Controller
{

    public function getTokensFirstTime () {
        try {
            $query = http_build_query([
                'client_id' => '1',
                'redirect_uri' => 'http://passport-cliente.test/callback',
                'response_type' => 'code',
                'scope' => 'show-products',
            ]);
        } catch (\Exception $exception) {
            return response()->json($exception->getMessage(), $exception->getCode());
        }

        return redirect('http://passport-api.test/oauth/authorize'. '?'.$query );
    }

    public function callback () {
        /*
        if (request('error') && request('error') === 'access_denied') {
            return response()->json('acceso denegado', 401);
        }
        */
        try{

            $http = new Client;
            $response = $http->post("http://passport-api.test/oauth/token", [
                'verify' => false,
                'form_params' => [
                    'grant_type' => 'authorization_code',
                    'client_id' => '1',
                    'client_secret' => 'jKvzgfIkjjlokw2oaEzRXulvFsGKNfpD9VCq9klC',
                    'redirect_uri' => 'http://passport-cliente.test/callback',
                    'code' => request('code'),
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            $app = App::firstOrCreate(['id' => 1]);
            $app->access_token = $data['access_token'];
            $app->refresh_token = $data['refresh_token'];
            $app->save();

            session()->flash('status', 'Los tokens se han generado y guardado satisfactoriamente');
            return redirect('/home');

        }catch (\Exception $e)
        {
            return response()->json($e->getMessage(), $e->getCode());
        }

    }

    public function refreshToken () {
        $http = new Client;
        $response = null;
        try {
            $app = App::first();
            $response = $http->post("http://passport-api.test/oauth/token", [
                'verify' => false,
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'client_id' => '1',
                    'client_secret' => 'jKvzgfIkjjlokw2oaEzRXulvFsGKNfpD9VCq9klC',
                    'redirect_uri' => 'http://passport-cliente.test/callback',
                    'refresh_token' => $app->refresh_token
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            if ($data) {
                $app->access_token = $data['access_token'];
                $app->refresh_token = $data['refresh_token'];
                $app->save();
            }
        } catch (ClientException $exception) {
            session()->flash('status', 'Los tokens expirado, conecte de nuevo con la API');
            return redirect('/home');
        }

        return back();
    }
}
