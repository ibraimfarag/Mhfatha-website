<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use  App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function dashboard_user(Request $request)
    {
        $user = Auth::user();

        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }
     
        return view('FrontEnd.profile.user-dashboard', compact('user'));
    }

    public function showProfile(Request $request)
{
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }
    $user = Auth::user(); // Assuming you're using Laravel's built-in Auth
    return view('FrontEnd.profile.profile', compact('user'));
}

}
