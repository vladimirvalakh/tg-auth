<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\ApiController;

/*
|--------------------------------------------------------------------------
| API V1 Routes
|--------------------------------------------------------------------------
*/
Route::get('/v1/healthcheck', 'App\Http\Controllers\Api\v1\ApiController@healthcheck');
Route::post('v1/order/send', 'App\Http\Controllers\Api\v1\OrderController@send');

