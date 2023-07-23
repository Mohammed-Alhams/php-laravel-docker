<?php

use App\Http\Controllers\Api\Auth\ApiAccessTokensController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\PharmacistController;
use App\Http\Controllers\Api\Pharmacy\InvoiceController;
use App\Http\Controllers\Api\Pharmacy\PharmacyController;
use App\Http\Controllers\Api\Pharmacy\StockController;

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

Route::apiResource("/pharmacy", PharmacyController::class);

Route::group(['middleware' => ['auth:sanctum', 'cors']], function () {
    Route::apiResource("/stocks", StockController::class);

    Route::apiResource("/invoices", InvoiceController::class);

//    Route::apiResource("/account", PharmacistController::class);
});
