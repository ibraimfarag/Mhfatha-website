<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\App;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchLanguage(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        // Redirect back to the previous page or a specific route
        return redirect()->back();
    }
}
