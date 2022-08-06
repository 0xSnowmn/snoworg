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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'api'], function () {
    Route::group(['prefix' => 'auth'], function () {
        Route::post('/login', 'AuthController@login');
        Route::post('/register','AuthController@register');
    });
    Route::group(['prefix' => 'programs'],function () {
        Route::post('/create', 'ProgramsController@create');
        Route::put('/update/{id}', 'ProgramsController@update');
        Route::delete('/delete/{id}', 'ProgramsController@delete');
        Route::get('/all', 'ProgramsController@all');
    });
    Route::group(['prefix' => 'updates'],function () {
        Route::post('/create', 'UpdatesController@create');
        Route::put('/update/{id}', 'UpdatesController@update');
        Route::delete('/delete/{id}', 'UpdatesController@delete');
        Route::get('/all', 'UpdatesController@all');
    });
    Route::group(['prefix' => 'activates'],function () {
        Route::post('/active_request', 'ActivatesController@activateRequest');
        Route::post('/create', 'ActivatesController@create');
        Route::post('/test', 'ActivatesController@test');
        Route::post('/reactivate', 'ActivatesController@reactivate');
        Route::post('/stop_key', 'ActivatesController@stop_key');
        Route::post('/login', 'ActivatesController@login');
        Route::post('/confirm', 'ActivatesController@confirm');
        Route::post('/request', 'ActivatesController@request');
        Route::put('/update/{id}', 'ActivatesController@update');
        Route::delete('/delete/{id}', 'ActivatesController@delete');
        Route::get('/all', 'ActivatesController@all');
        Route::get('/waiting', 'ActivatesController@waiting');
    });

    Route::group(['prefix' => 'short'],function () {
        Route::post('/create', 'UrlShorterController@create');
        Route::put('/update/{id}', 'UrlShorterController@update');
        Route::delete('/delete/{id}', 'UrlShorterController@delete');
        Route::get('/all', 'UrlShorterController@all');
        Route::get('/{url}', 'UrlShorterController@short');
    });
    Route::group(['prefix' => 'info'],function () {
        Route::post('/create', 'InfoController@create');
        Route::put('/update/{id}', 'InfoController@update');
        Route::delete('/delete/{id}', 'InfoController@delete');
        Route::get('/all', 'InfoController@all');
    });
    
    /* Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/profile', [AuthController::class, 'userProfile']);  
    Route::resource('/products', ProductController::class); */  
});
