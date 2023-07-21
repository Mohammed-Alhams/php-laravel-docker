<?php

use App\Http\Controllers\Api\ApiAccessTokensController;
use App\Http\Controllers\Api\auth\RegisterController;
use App\Http\Controllers\Api\PharmacistController;
use App\Http\Controllers\Api\StockController;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post("auth/access-tokens", [ApiAccessTokensController::class, 'store'])
    ->middleware('guest:sanctum');

Route::controller(RegisterController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
});

Route::apiResource("/stocks", StockController::class);

Route::apiResource("/account", PharmacistController::class);
