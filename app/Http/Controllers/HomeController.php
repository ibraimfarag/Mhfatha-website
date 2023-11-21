<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\WebsiteManager;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }
        $websiteManager = WebsiteManager::first();

        // Your controller logic for the home page

        return view('FrontEnd.home',compact('websiteManager')); // Make sure to return the appropriate view
    }
}