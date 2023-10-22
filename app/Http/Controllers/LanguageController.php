<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchToEnglish()
    {
        app()->setLocale('en');
        return redirect()->back(); // Redirect back to the previous page.
    }
    
    public function switchToArabic()
    {
        app()->setLocale('ar');
        return redirect()->back();
    }
}
