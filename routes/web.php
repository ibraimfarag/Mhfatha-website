<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\App;

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

// Route::get('/', function () {
//     return view('FrontEnd.home');
// });

Route::get('/', function () {
    $languages = ['en', 'ar']; // Define your supported languages
    $userLanguages = explode(',', request()->server('HTTP_ACCEPT_LANGUAGE'));
    foreach ($userLanguages as $userLang) {
        $lang = substr($userLang, 0, 2);
        if (in_array($lang, $languages)) {
            App::setLocale($lang); // Set the detected language as the application's locale
            return redirect('/' . $lang);
        }
    }
    // If no supported language is detected, set a default language (e.g., 'ar')
    App::setLocale('ar');
    return redirect('/ar');
});
Route::get('/{lang?}/', [HomeController::class,'index'])->name('home');




Route::prefix('username/cpanel')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('user.login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('user.register');
    Route::post('/register', [RegisterController::class, 'register']);
});

// Admin Backend
Route::prefix('admin')->group(function () {
    // Add your admin routes here
});
