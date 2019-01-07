<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

//obtiene el token por primera vez, pantalla acceso API
Route::get('/get-tokens-first-time', 'Api\AuthenticationController@getTokensFirstTime');

//aquí somos redirigidos al tomar los tokens, guardamos los datos en la tabla app
Route::get('/callback', 'Api\AuthenticationController@callback');

//si el token ha expirado, utilizamos el refresh token para obtener unos nuevos
Route::get('/refresh-token', 'Api\AuthenticationController@refreshToken');

//obtenemos nuestros datos de la API enviando el token Bearer
Route::get('/user', 'Api\UserController@me');
