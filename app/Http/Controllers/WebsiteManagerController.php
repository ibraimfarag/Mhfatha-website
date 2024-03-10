<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\WebsiteManager;
use App\Models\User;
use App\Models\Store;
use App\Models\UserDiscount;
use App\Models\Region;
use App\Models\StoreCategory;
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

                        ->where('obtained_status', 0)
                        ->update(['obtained_status' => 1]);
                }
                break;
            default:
                return response()->json(['error' => 'Invalid query']);
        }

        return response()->json(['message' => 'Discounts accepted successfully']);
    }
    public function manageRecords(Request $request)
    {


        $action = $request->input('action');
        $modelName = $request->input('modelName');
        // $data = json_decode($request->input('data'), true); 
        $data = $request->input('data');

        if (is_array($data)) {
            // Data is already an array, no need to decode it
            $jsonData = $data;
        } else {
            // Decode the JSON string
            $jsonData = json_decode($data, true);
        }
        switch ($modelName) {
            case 'Region':
                switch ($action) {
                    case 'add':
                        $region = new Region();
                        $region->fill($data);
                        $region->save();
                        return "Region added successfully.";
                        break;
                    case 'delete':
                        $region = Region::find($data['id']);
                        if ($region) {
                            $region->delete();
                            return "Region deleted successfully.";
                        } else {
                            return "Region not found.";
                        }
                        break;
                    case 'edit':
                        $region = Region::find($data['id']);
                        if ($region) {
                            $region->fill($data);
                            $region->save();
                            return "Region updated successfully.";
                        } else {
                            return "Region not found.";
                        }
                        break;
                    default:
                        return "Invalid action.";
                }
                break;

            case 'StoreCategory':
                switch ($action) {
                    case 'add':
                        $storeCategory = new StoreCategory();
                        $storeCategory->fill($data);
                        $storeCategory->save();
                        return "Store category added successfully.";
                        break;
                    case 'delete':
                        $storeCategory = StoreCategory::find($data['id']);
                        if ($storeCategory) {
                            $storeCategory->delete();
                            return "Store category deleted successfully.";
                        } else {
                            return "Store category not found.";
                        }
                        break;
                    case 'edit':
                        $storeCategory = StoreCategory::find($data['id']);
                        if ($storeCategory) {
                            $storeCategory->fill($data);
                            $storeCategory->save();
                            return "Store category updated successfully.";
                        } else {
                            return "Store category not found.";
                        }
                        break;
                    default:
                        return "Invalid action.";
                }
                break;
            case 'WebsiteManager':
                switch ($action) {
                    case 'update':
                        // Retrieve the WebsiteManager record
                        $websiteManager = WebsiteManager::first();

                        // Validate and update the fields
                        $rules = [
                            'commission' => 'string',
                            'map_distance' => 'string',
                            // Add rules for other fields as needed
                        ];

                        // Validate the data
                        $validatedData = validator($jsonData, $rules)->validate();

                        // Update the WebsiteManager record
                        $websiteManager->update($validatedData);

                        // return "Website Manager information updated successfully.";
                        return response()->json(['message' =>'Website Manager information updated successfully']);

                        break;

                    default:
                        return "Invalid action.";
                }
                break;

            default:
                return "Invalid model name.";
        }
    }
}
