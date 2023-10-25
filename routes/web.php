<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
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
//     $languages = ['en', 'ar']; // Define your supported languages
//     $userLanguages = explode(',', request()->server('HTTP_ACCEPT_LANGUAGE'));
//     foreach ($userLanguages as $userLang) {
//         $lang = substr($userLang, 0, 2);
//         if (in_array($lang, $languages)) {
//             App::setLocale($lang); // Set the detected language as the application's locale
//             return redirect('/' . $lang);
//         }
//     }
//     // If no supported language is detected, set a default language (e.g., 'ar')
//     App::setLocale('ar');
//     return redirect('/ar');
// });
Route::get('/home', [HomeController::class,'index'])->name('home');

Route::get('/login', [AuthController::class, 'login_index'])->name('login');
Route::get('/register', [AuthController::class, 'register_index'])->name('register');

Route::get('/switch-language', [LanguageController::class, 'switchLanguage'])->name('switchLanguage');
