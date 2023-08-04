<?php

use App\Http\Controllers\Api\Auth\ApiAccessTokensController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Pharmacy\AnalyticsController;
use App\Http\Controllers\Api\Pharmacy\CheckoutController;
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

    Route::apiResource("/invoices", CheckoutController::class);

    Route::controller(AnalyticsController::class)->group(function(){
        Route::get('best-selling', 'bestSelling');
        Route::get('restocking-analysis', 'restockingAnalysis');
        Route::get('sells-summary', 'sellsTotalPricesSummary');
        Route::get('average-sells', 'averageTransactionValue');
        Route::get('total-revenue', 'totalSalesRevenue');
        Route::get('expired-stocks', 'expiredStocks');
        Route::get('low-performing-stocks', 'lowPerformingStocks');
        Route::get('customer-preferences', 'customerPreferences');
        Route::get('operational-efficiency', 'operationalEfficiency');
        Route::get("busiest-times", "busiestTimes");
        Route::get('all-analysis', 'allAnalysis');
        Route::get('busiest-days', 'busiestDays');
    });
});
