<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\UserDiscountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomEncrypter;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;

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

Route::get('check-network', [AuthController::class, 'checkInternetConnection']);
Route::post('/registerregions', [UserController::class, 'getRegionsAndCitiesApi']);
Route::post('/auth/resetPassword', [UserController::class, 'resetPassword']);
Route::post('/regions', [UserController::class, 'getRegionsAndCitiesApi']);
Route::post('/categories', [UserController::class, 'getcategoryApi']);

Route::get('/update-discounts', [UserDiscountController::class, 'checkDiscountsExpiration']);
Route::middleware('auth:api')->group(function () {


    Route::get('/user', [UserController::class, 'getUserInfoApi']);
    Route::post('/auth/changepassword', [UserController::class, 'changePassword']);
    Route::post('/auth/update', [UserController::class, 'updateProfileWithOtp']);
    Route::post('/nearby', [StoreController::class, 'nearbyApi']);
    Route::post('/store', [StoreController::class, 'storeInfoApi']);
    Route::post('/stores/search-by-name', [StoreController::class, 'searchByNameApi']);
    Route::post('/store-qr', [StoreController::class, 'decryptQrCode']);
    Route::post('/discounts-post', [UserDiscountController::class, 'postUserDiscount']);
    Route::post('/user-discounts', [UserDiscountController::class, 'getAllUserDiscounts']);
    Route::post('/filter-stores', [StoreController::class, 'filterStoresApi']);

    // /* --------------------------------- vendor --------------------------------- */
    Route::post('/vendor/stores', [StoreController::class, 'userStores']);
    Route::post('/vendor/store/create', [StoreController::class, 'createStore']);
});
