<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\WebsiteManager;
use Illuminate\Support\Facades\App;


class WebsiteManagerController extends Controller
{
    


    public function GeneralSection(Request $request)
{
   
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }
    $websiteManager = WebsiteManager::first();
    // $test=  $websiteManager->first()->site_title['ar'];
// dd($test);
    return view('Backend.general', compact('websiteManager'));
}
    public function HeroSection(Request $request)
{
   
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }
    $websiteManager = WebsiteManager::first();


    return view('Backend.hero', compact('websiteManager'));
}
    public function AboutSection(Request $request)
{
   
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }
    $websiteManager = WebsiteManager::first();


    return view('Backend.about', compact('websiteManager'));
}
    public function AdvantagesSection(Request $request)
{
   
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }
    $websiteManager = WebsiteManager::first();


    return view('Backend.advantages', compact('websiteManager'));
}
public function AppSection(Request $request)
{
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }
    // Retrieve the website manager record (assuming there's only one record)
    $websiteManager = WebsiteManager::first();

    return view('Backend.app', compact('websiteManager'));
}
public function update(Request $request)
{
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }

    // Retrieve the website manager record (assuming there's only one record)
    $websiteManager = WebsiteManager::first();

    // Validation rules
    $rules = [
        'site_title.ar' => 'required|string',
        'site_title.en' => 'required|string',
        'site_description.ar' => 'required|string',
        'site_description.en' => 'required|string',
        'site_meta_keywords.ar' => 'string',
        'site_meta_keywords.en' => 'string',
            'site_logo' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Example rule for site logo, adjust as needed
        // Add rules for other fields in the General section
    ];

    // Validate and update the fields
    $validatedData = $request->validate($rules);
    
    // Additional processing for specific fields (e.g., uploading files)
    if ($request->hasFile('site_logo')) {
        $logoPath = $request->file('site_logo')->store('logos', 'public');
        dd($logoPath); // Add this line to check the path
        $validatedData['site_logo'] = $logoPath;
    }
    
    // Update the Website Manager record
    $websiteManager->update($validatedData);
    // dd(session()->all());
    return redirect()->back()->with('success', __('Website Manager information updated successfully.'), ['lang' => $request->input('lang')]);
}

}
