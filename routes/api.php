<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('user/login', 'UserRegistrationController@login');
Route::post('user/register', 'UserRegistrationController@store');
Route::group(['middleware' => 'auth:api'], function (){
    Route::resource('user', 'UserRegistrationController');
    Route::post('user/update/{id}', 'UserRegistrationController@updateDetails');
    Route::get('user/rolebyuser/{name}', 'UserRegistrationController@roleByUser');
});
