<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\UserController;
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
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

    
});

Route::middleware(['vendor'])->group(function () {
    // Routes accessible only to vendors
    // Route::get('/register', [AuthController::class, 'register_index'])->name('register');    // Add other vendor-specific routes here
});

Route::middleware(['admin'])->group(function () {
    // Routes accessible only to admins
    // Route::get('/admin-dashboard', 'AdminController@index');
    // Add other admin-specific routes here
});

