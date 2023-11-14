<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserDiscountController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DiscountController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('main');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/switch-language', [LanguageController::class, 'switchLanguage'])->name('switchLanguage');

// Guest routes
Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'login_index'])->name('login');
    Route::post('/login-post', [AuthController::class, 'login_post'])->name('login_post');
    Route::get('/register', [AuthController::class, 'register_index'])->name('register');
    Route::post('/register-post', [AuthController::class, 'register_post'])->name('register_post');
});

// Authenticated user routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard_user'])->name('dashboard_user');
    Route::get('/showProfile', [UserController::class, 'showProfile'])->name('profile');
    Route::put('/update-profile', [UserController::class, 'update_profile'])->name('profile-update');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/password/change', [PasswordController::class, 'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [PasswordController::class, 'changePassword'])->name('password.change');

    Route::get('/discounts/home', [UserDiscountController::class, 'view'])->name('discount.view');


    Route::get('/stores/nearby', [StoreController::class, 'nearby'])->name('stores.nearby');
  

    
});

// Vendor routes
Route::middleware(['vendor'])->group(function () {
    Route::get('/stores', [StoreController::class, 'index'])->name('Stores.view');
    Route::get('/stores/create', [StoreController::class, 'create'])->name('stores.create');
    Route::post('/stores/store', [StoreController::class, 'store'])->name('stores.store');
    Route::delete('stores/{store}', [StoreController::class, 'destroy'])->name('stores.destroy');
    Route::get('/stores/edit', [StoreController::class, 'edit'])->name('stores.edit');
    Route::put('/stores/{store}', [StoreController::class, 'update'])->name('stores.update');

 
    Route::get('/get-badge-counts', [DiscountController::class, 'getBadgeCounts'])->name('get.Badge.Counts');
    Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
    Route::get('/discounts/create/', [DiscountController::class, 'create'])->name('discounts.create');
    Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');
    Route::put('/discounts/{discount}', [DiscountController::class, 'update'])->name('discounts.update');
    Route::delete('/discounts/{discount}', [DiscountController::class, 'destroy'])->name('discounts.destroy');

    Route::get('/vendor/discount-chart-data/{timePeriod}', [UserController::class, 'getDiscountChartData']);

    Route::get('/download-merged-image/{storeId}', [StoreController::class, 'downloadMergedImage'])->name('download.merged.image');

});



/* -------------------------------------------------------------------------- */
/* ----------------------------- // Admin routes ---------------------------- */
/* -------------------------------------------------------------------------- */
Route::middleware(['admin'])->group(function () {
    

    //  /* ---------------------------- view users manger --------------------------- */
    Route::get('/admin/users', [UserController::class, 'showProfiles'])->name('users.index');
    Route::get('/users/filter', [UserController::class, 'fetchUsers'])->name('users.fetch');
    Route::get('/users/edit', [UserController::class, 'showProfile_user'])->name('users.edit');
    Route::put('/users/update-profile/', [UserController::class, 'updateAdminProfile'])->name('users.update-profile');
    


    // /* ----------------------------- view discounts ----------------------------- */

    Route::get('/admin/discounts/home', [UserDiscountController::class, 'view_admin'])->name('discounts.admin.view');
    Route::get('/user/discounts/fetch', [UserDiscountController::class, 'fetchDiscounts'])->name('discounts.fetch');
    Route::get('/user/store/overview', [UserDiscountController::class, 'store_overview'])->name('store.overview');



    // /* ---------------------------------- store --------------------------------- */
    Route::put('stores/{store}/verify', [StoreController::class, 'verify'])->name('stores.verify');



    

});
