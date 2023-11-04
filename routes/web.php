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

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/






Route::get('/', [HomeController::class,'index'])->name('main');
Route::get('/home', [HomeController::class,'index'])->name('home');

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'login_index'])->name('login');
    Route::post('/login-post', [AuthController::class, 'login_post'])->name('login_post');
    Route::get('/register', [AuthController::class, 'register_index'])->name('register');
    Route::post('/register-post', [AuthController::class, 'register_post'])->name('register_post');
});

Route::get('/switch-language', [LanguageController::class, 'switchLanguage'])->name('switchLanguage');



Route::middleware(['auth'])->group(function () {


    Route::get('/dashboard', [UserController::class, 'dashboard_user'])->name('dashboard_user');
    Route::get('/showProfile', [UserController::class, 'showProfile'])->name('profile');
    Route::put('/update-profile', [UserController::class, 'update_profile'])->name('profile-update');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::get('/password/change', [PasswordController::class,'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [PasswordController::class,'changePassword'])->name('password.change');


    Route::get('/discounts/home', [UserDiscountController::class,'view'])->name('discount.view');






});

Route::middleware(['vendor'])->group(function () {

    Route::get('/stores', [StoreController::class,'index'])->name('Stores.view');

    Route::get('/stores/create', [StoreController::class,'create'])->name('stores.create');
    Route::post('/stores/store', [StoreController::class,'store'])->name('stores.store');


    Route::delete('stores/{store}', [StoreController::class,'destroy'])->name('stores.destroy');



    Route::get('/stores/edit', [StoreController::class, 'edit'])->name('stores.edit');

    Route::put('/stores/{store}', [StoreController::class, 'update'])->name('stores.update');
    
    Route::put('stores/{store}/verify', [StoreController::class, 'verify'])->name('stores.verify');
    
    Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
    Route::get('/discounts/create/', [DiscountController::class, 'create'])->name('discounts.create');
Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');
Route::put('/discounts/{discount}', [DiscountController::class, 'update'])->name('discounts.update');
Route::delete('/discounts/{discount}', [DiscountController::class, 'destroy'])->name('discounts.destroy');


});

Route::middleware(['admin'])->group(function () {

});

