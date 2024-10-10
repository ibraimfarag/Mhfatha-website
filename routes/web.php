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
use App\Http\Controllers\WebsiteManagerController;
use App\Http\Controllers\TermsAndConditionsPolicyController;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;
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
Route::get('/', function () {
    return view('FrontEnd.soon');
})->name('main');

