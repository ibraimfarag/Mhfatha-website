<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\UserDiscountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomEncrypter;
use App\Http\Controllers\StoreController;

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

Route::get('/discounts', [DiscountController::class, 'index_api']);


Route::post('login-post', [AuthController::class, 'login_api']);

Route::post('register-post', [AuthController::class, 'register_api']);

Route::middleware('auth:api')->group(function () {

    Route::post('/nearby', [StoreController::class, 'nearbyApi']);
    Route::post('/store', [StoreController::class, 'storeInfoApi']);
    Route::post('/store-qr', [StoreController::class, 'decryptQrCode']);
    Route::post('/discounts-post', [UserDiscountController::class, 'postUserDiscount']);
    Route::post('/user-discounts', [UserDiscountController::class, 'getAllUserDiscounts']);

});
