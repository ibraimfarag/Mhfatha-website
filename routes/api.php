<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\UserDiscountController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CustomEncrypter;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WebsiteManagerController;
use App\Http\Controllers\RequestsController;
use App\Http\Controllers\TermsAndConditionsPolicyController;
use App\Http\Controllers\ComplaintsSuggestionsParentController;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

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

Route::get('/send-test-email', function () {
    Mail::to('ib.farag@gmail.com')->send(new TestEmail());
    return 'Email has been sent!';
});


Route::get('/discounts', [DiscountController::class, 'index_api']);


Route::post('login-post', [AuthController::class, 'login_api']);
Route::post('register-post', [AuthController::class, 'register_api']);
Route::get('clear', [AuthController::class, 'clearCache']);
Route::post('validateToken', [AuthController::class, 'validateToken']);
Route::post('/registerregions', [UserController::class, 'getRegionsAndCitiesApi']);
Route::post('/auth/resetPassword', [UserController::class, 'resetPassword']);


Route::get('check-network', [AuthController::class, 'checkInternetConnection']);
Route::post('/regions', [UserController::class, 'getRegionsAndCitiesApi']);
Route::post('/categories', [UserController::class, 'getcategoryApi']);

Route::get('/update-discounts', [UserDiscountController::class, 'checkDiscountsExpiration']);

Route::post('/checkversion', [WebsiteManagerController::class, 'getVersion']);
Route::get('/time-and-date', [WebsiteManagerController::class, 'getTimeAndDate']);

Route::post('/TermsAndConditions', [TermsAndConditionsPolicyController::class, 'getTermsAndConditions']);



Route::post('/nearby', [StoreController::class, 'nearbyApi']);
Route::post('/store', [StoreController::class, 'storeInfoApi']);
Route::post('/stores/search-by-name', [StoreController::class, 'searchByNameApi']);
Route::post('/filter-stores', [StoreController::class, 'filterStoresApi']);


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



Route::middleware('auth:api')->group(
    function () {
        Route::get('/contact-us', [WebsiteManagerController::class, 'manageContacts']);

        Route::prefix('supporting/')->group(function () {
            Route::post('/get-resons', [ComplaintsSuggestionsParentController::class, 'getComplaintsSuggestionsOptions']);
            Route::post('/create', [ComplaintsSuggestionsParentController::class, 'store']);
            Route::post('/get', [ComplaintsSuggestionsParentController::class, 'getAllUserComplaintSuggestions']);
            Route::post('/update', [ComplaintsSuggestionsParentController::class, 'update']);
            Route::post('/changeStatus', [ComplaintsSuggestionsParentController::class, 'changeStatus']);
        });

        Route::post('/update-device-info', [UserController::class, 'updateDeviceInfo']);
        Route::get('/user', [UserController::class, 'getUserInfoApi']);
        Route::post('/auth/changepassword', [UserController::class, 'changePassword']);
        Route::post('/auth/update', [UserController::class, 'updateProfileWithOtp']);
        Route::post('/auth/updateAuthUserStatus', [UserController::class, 'updateAuthUserStatus']);

        Route::post('/store-qr', [StoreController::class, 'decryptQrCode']);
        Route::post('/discounts-post', [UserDiscountController::class, 'postUserDiscount']);
        Route::post('/user-discounts', [UserDiscountController::class, 'getAllUserDiscounts']);


        // /* --------------------------------- vendor --------------------------------- */

        Route::post('vendor/stores', [StoreController::class, 'userStores']);

        Route::prefix('vendor/store')->group(function () {
            Route::post('create', [StoreController::class, 'createStore']);
            Route::post('edit', [StoreController::class, 'updateStore']);
            Route::post('delete', [StoreController::class, 'deleteStore']);
            Route::post('qr', [StoreController::class, 'MergedImageQr']);
            Route::post('discounts', [DiscountController::class, 'getDiscountsByStoreId']);
            Route::post('discounts/create', [DiscountController::class, 'createStoreDiscount']);
            Route::post('discounts/delete', [DiscountController::class, 'createDeleteDiscountRequest']);
        });



        Route::prefix('admin')->group(function () {

            Route::get('users', [UserController::class, 'getAllUsers']);
            Route::get('statistics', [UserController::class, 'getUsersStatistics']);


            Route::post('actions', [UserController::class, 'updateUserStatus']);
            Route::post('user/update', [UserController::class, 'updateUser']);


            Route::post('store/actions', [StoreController::class, 'manageStore']);

            Route::post('requests', [RequestsController::class, 'MyRequests']);
            Route::post('sendnotification', [RequestsController::class, 'sendNotification']);

            Route::post('accounts', [WebsiteManagerController::class, 'acceptDiscounts']);

            Route::post('sets', [WebsiteManagerController::class, 'manageRecords']);

            Route::post('/TermsAndConditions', [TermsAndConditionsPolicyController::class, 'updateTermsAndConditions']);
        });
    }

);
