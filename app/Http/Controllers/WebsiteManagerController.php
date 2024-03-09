<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\WebsiteManager;
use App\Models\User;
use App\Models\Store;
use App\Models\UserDiscount;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use App\Models\AppUpdate;


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
        'site_meta_keywords.en' => 'string',
        'site_meta_keywords.en' => 'string',
        'map_distance' => 'string',
        'commission' => 'string',
        
        // Add rules for other fields in the General section
    ];

    // Validate and update the fields
    $validatedData = $request->validate($rules);
    
 

    if ($request->hasFile('site_favicon')) {
        // Delete the old favicon image (if it exists)
        if ($websiteManager->site_favicon) {
            $oldFaviconPath = public_path('FrontEnd\assets\images\logos' . $websiteManager->site_favicon);
            if (File::exists($oldFaviconPath)) {
                File::delete($oldFaviconPath);
            }
        }
    
        // Store the new favicon image
        $favicon = $request->file('site_favicon');
        $faviconName = time() . '_favicon.' . $favicon->getClientOriginalExtension();
        $favicon->move(public_path('FrontEnd\assets\images\logos'), $faviconName);
        $websiteManager->site_favicon = $faviconName;
    }
    
    if ($request->hasFile('site_logo')) {
        // Delete the old logo image (if it exists)
        if ($websiteManager->site_logo) {
            $oldLogoPath = public_path('FrontEnd\assets\images\logos' . $websiteManager->site_logo);
            if (File::exists($oldLogoPath)) {
                File::delete($oldLogoPath);
            }
        }
    
        // Store the new logo image
        $logo = $request->file('site_logo');
        $logoName = time() . '_logo.' . $logo->getClientOriginalExtension();
        $logo->move(public_path('FrontEnd\assets\images\logos'), $logoName);
        $websiteManager->site_logo = $logoName;
    }
    
    

    
    // Update the Website Manager record
    $websiteManager->update($validatedData);
    // dd(session()->all());
    return redirect()->back()->with('success', __('Website Manager information updated successfully.'), ['lang' => $request->input('lang')]);
}


public function getVersion(Request $request)
{
    // Validate the input
    $request->validate([
        'platform' => 'required|in:iOS,Android',
    ]);

    // Get the platform from the request
    $platform = $request->input('platform');

    // Initialize variables to hold version and required status
    $version = null;
    $required = null;

    // Retrieve version information based on the platform
    if ($platform === 'iOS') {
        $update = AppUpdate::select('ios_version', 'ios_required')->first();
        if ($update) {
            $version = $update->ios_version;
            $required = $update->ios_required;
        }
    } elseif ($platform === 'Android') {
        $update = AppUpdate::select('android_version', 'android_required')->first();
        if ($update) {
            $version = $update->android_version;
            $required = $update->android_required;
        }
    }

    // Prepare response
    $response = [
        'platform' => $platform,
        'version' => $version,
        'required' => $required,
    ];

    // Return response
    return response()->json($response);
}


public function acceptDiscounts(Request $request)
{
    // Retrieve store_id from the request
    $storeId = $request->input('storeId');

    // Retrieve the input query
    $query = $request->input('query');

    // Handle different cases based on the input query
    switch ($query) {
        case 'accept_all':
            // Accept all UserDiscounts for the specified store
            UserDiscount::where('store_id', $storeId)
                ->where('obtained_status', 0)
                ->update(['obtained_status' => 1]);
            break;
        case 'selected':
            // Retrieve the list of UserDiscount IDs from the request
            $userDiscountIds = $request->input('user_discount_ids');

            // Accept selected UserDiscounts
            if (!empty($userDiscountIds)) {
                UserDiscount::whereIn('id', $userDiscountIds)
                    ->where('store_id', $storeId)
                    ->where('obtained_status', 0)
                    ->update(['obtained_status' => 1]);
            }
            break;
        default:
            return response()->json(['error' => 'Invalid query']);
    }

    return response()->json(['message' => 'Discounts accepted successfully']);
}

}
