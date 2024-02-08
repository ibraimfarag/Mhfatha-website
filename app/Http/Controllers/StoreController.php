<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;
use App\Models\StoreCategory;
use App\Models\Region; // Import the Region model
use App\Models\WebsiteManager;
use App\Models\City;
use Illuminate\Validation\ValidationException;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        $user = auth()->user(); // Get the logged-in user

        // Check if the user is an admin
        if ($user->is_admin) {
            $query = Store::with(['user', 'userDiscounts']);

            // Apply filters
            if ($request->has('status')) {
                $query->where('status', $request->input('status'));
            }

            // Search by store name or user name (in both languages)
            if ($request->has('search')) {
                $searchTerm = $request->input('search');
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('name', 'like', '%' . $searchTerm . '%')
                        ->orwhere('phone', 'like', '%' . $searchTerm . '%')
                        ->orWhereHas('user', function ($u) use ($searchTerm) {
                            $u->where('first_name', 'like', '%' . $searchTerm . '%')
                                ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                                ->orWhere('mobile', 'like', '%' . $searchTerm . '%');
                        });
                });
            }
            if ($request->has('status') && $request->input('status') === null) {
                $query->getQuery()->orders = [];
            }
            $userStores = $query->paginate(5);
        } else {
            // User is not an admin, return stores associated with their user ID
            $userStores = Store::where('user_id', $user->id)->where('is_deleted', 0)->paginate(10);
        }

        return view('FrontEnd.profile.stores.index', ['userStores' => $userStores, 'lang' => $lang]);
    }
    public function create(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }


        $user = auth()->user(); // Get the logged-in user
        $userStores = Store::where('user_id', $user->id)->get(); // Retrieve stores associated with the user


        return view('FrontEnd.profile.stores.create', compact('userStores'));
    }
    public function decryptQrCode(Request $request)
    {
        try {
            // Retrieve the encrypted store ID and language from the JSON request body
            $encryptedStoreID = $request->json('encryptedStoreID');
            $lang = $request->json('lang', 'en'); // Default to English if lang is not provided

            $customEncrypter = new CustomEncrypter();
            $decryptedStoreID = $customEncrypter->customDecode($encryptedStoreID);


            // Fetch store information from your data source (adjust the model and attribute names)
            $store = Store::find($decryptedStoreID);

            if (!$store) {
                return response()->json(['error' => $lang === 'ar' ? 'لم يتم العثور على المتجر.' : 'Store not found.'], 404);
            }

            // Check verification status
            if ($store->verification === 0) {
                $message = $lang === 'ar' ? 'جاري التحقق من المتجر.' : 'Store verification is pending.';
                return response()->json(['message' => $message], 200);
            }

            // Check if the store is banned
            if ($store->is_bann === 1) {
                $message = $lang === 'ar' ? 'هذا المتجر مغلق مؤقتًا من جهة الادارة.' : 'This store is temporarily closed by management.';
                return response()->json(['message' => $message], 200);
            }

            // Check store status
            if ($store->status === 0) {
                $message = $lang === 'ar' ? 'هذا المتجر مغلق مؤقتًا من قبل التاجر.' : 'This store is temporarily closed by the merchant.';
                return response()->json(['message' => $message], 200);
            }
            $discounts = $store->Discounts->where('discounts_status', 'working')->where('is_deleted', 0);

            // Check discount attributes
            if ($discounts->isNotEmpty()) {
                return response()->json(['discounts' => $discounts->all(), 'store' => $store]);
            } else {
                $message = $lang === 'ar' ? 'لا توجد خصومات متاحة.' : 'No discount available.';
                return response()->json(['message' => $message, 'store' => $store], 200);
            }
        } catch (\Exception $e) {
            // Handle decryption errors
            $errorMessage = $lang === 'ar' ? 'فشل في التعرف على المتجر.' : 'Failed to recognize the store.';
            return response()->json(['error' => $errorMessage], 500);
        }
    }

    public function generateQrCode($storeID)
    {
        $customEncrypter = new CustomEncrypter(); // Instantiate the class
        $encryptedStoreID = $customEncrypter->customEncode($storeID);

        // Generate QR code
        $qrCode = QrCode::size(300)->format('png')->generate($encryptedStoreID);

        // Get the filename from the full path
        $filename = 'qr_code_' . $storeID . '.png';

        // Save the QR code image with the filename
        $qrCodePath = public_path('FrontEnd/assets/images/stores_qr/') . $filename;
        file_put_contents($qrCodePath, $qrCode);

        // Update the store record with the filename
        $store = Store::find($storeID);
        $store->qr = $filename;
        $store->save();
    }
    private function mergeImages($backgroundPath, $qrPath, $outputPath, $qrSize = 100, $qrPosition = ['x' => 0, 'y' => 0])
    {
        // Load the background image
        $background = Image::make($backgroundPath);

        // Load the QR code image
        $qrCode = Image::make($qrPath);

        // Resize the QR code to the specified size
        $qrCode->resize($qrSize, $qrSize);

        // Paste the QR code onto the background at the specified position
        $background->insert($qrCode, 'top-left', $qrPosition['x'], $qrPosition['y']);

        // Save the merged image to the output path
        $background->save($outputPath);
    }
    public function downloadMergedImage(Request  $request)
    {    $storeId = $request->input('storeId');
        // Get the store information (adjust the logic to fit your needs)
        $store = Store::find($storeId);

        // Paths
        $backgroundPath = public_path('FrontEnd/assets/images/banner/background.png');
        $qrCodePath = public_path('FrontEnd/assets/images/stores_qr/') . $store->qr;
        $outputPath = public_path('FrontEnd/assets/images/stores_qr_banar/merged_image.png'); // Adjust this path as needed

        // Check if both images exist
        if (!file_exists($backgroundPath) || !file_exists($qrCodePath)) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        // Merge images using $this->mergeImages
        $this->mergeImages($backgroundPath, $qrCodePath, $outputPath, 355, ['x' => 469, 'y' => 2505]);

        // Download the merged image
        return response()->download($outputPath, 'merged_image.png');
    }
    public function store(Request $request)
    {
        $lang = $request->input('lang'); // Get the 'lang' parameter from the request

        $currentLanguage = $request->input('lang');



        // Check the language and set the appropriate error message
        if ($currentLanguage === 'ar') {
            $errorMessages = [
                'name.required' => 'يجب ادخال اسم المتجر ',
                'phone.unique' => 'رقم الجوال مستخدم بالفعل. يرجى اختيار رقم آخر.',
                'phone.required' => ' رقم الجوال مطلوب.',
                'location.required' => 'يجب ادخال العنوان تفصيلي.',
                'status.required' => 'يجب اختيار حالة المتجر .',
                'url_map.required' => 'يجب تحديد المتجر على الخريطة .',
                'work_days.required' => 'يجب تحديد ايام و مواعيد العمل  .',
            ];
        }
        if ($currentLanguage === 'en') {
            $errorMessages = [
                'name.required' => 'The store name field is required.',
                'phone.unique' => 'The mobile number is already in use. Please choose a different one.',
                'phone.required' => 'The mobile field is required.',
                'location.required' => 'The address field is required.',
                'status.required' => 'store status field is required.',
                'url_map.required' => 'store map field is required.',
                'work_days.required' => 'works time and days is required.',

            ];
        }


        $customMessages = $errorMessages;

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'location' => 'required|max:191',
            'phone' => 'required|max:10|min:10|unique:users,mobile|unique:stores,phone',
            'city' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            // 'url_map' => 'required',
            'photo' => 'nullable',
            'work_hours' => 'nullable|string',
            'work_days' => 'required|array',
            'status' => 'required|boolean',
        ], $customMessages);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $user = auth()->user();

        // Create a new store record
        $store = new Store;
        $store->name = $request->input('name');
        $store->location = $request->input('location');
        $store->phone = $request->input('phone');
        $store->url_map = $request->input('url_map');
        $store->photo = $request->input('photo');
        $store->work_hours = $request->input('work_hours');
        $store->status = $request->input('status');
        $store->city = $request->input('city');
        $store->region = $request->input('region');
        $store->latitude = $request->input('latitude');
        $store->longitude = $request->input('longitude');
        $store->user_id = $user->id;

        // Handle store image upload
        if ($request->hasFile('store_image')) {
            $this->handleStoreImageUpload($store, $request->file('store_image'));
        } else {
            // If no image is uploaded, set a default image
            $store->photo = 'null-market.png'; // Change 'null-market.png' to your default image filename
        }

        // Save the selected work days and their working hours
        $workDays = $request->input('work_days');
        $workDayHours = [];
        foreach ($workDays as $day) {
            $workDayHours[$day] = [
                'from' => $request->input($day . '_from'),
                'to' => $request->input($day . '_to'),
            ];
        }
        $store->work_days = json_encode($workDayHours);

        $store->save();
        $this->generateQrCode($store->id);

        return redirect()->route('Stores.view', ['lang' => $lang])->with('success', 'Store Added Successfully.');
    }

    private function handleStoreImageUpload($store, $image)
    {
        // Delete the old store image (if it exists)
        if ($store->photo) {
            $oldImagePath = public_path('store_images/' . $store->photo);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
        }

        // Store the new store image
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('FrontEnd/assets/images/store_images'), $imageName);
        $store->photo = $imageName;
    }
    public function edit(Request $request)
    {
        $lang = $request->input('lang');
        $storeid = $request->input('storeid'); // Retrieve the 'storeid' parameter

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        $store = Store::find($storeid);

        return view('FrontEnd.profile.stores.edit', ['store' => $store]);
    }
    public function update(Request $request, Store $store)
    {

        $lang = $request->input('lang');


        // Validate the incoming data
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'location' => 'required|max:191',
            'phone' => 'required|max:10|min:10',
            'url_map' => 'nullable',
            'store_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // You can adjust the validation rules as needed.
            'work_days' => 'nullable|array',
            'status' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->route('stores.edit', ['store' => $store->id, 'lang' => $request->input('lang')])->withErrors($validator);
        }

        $store->name = $request->input('name');
        $store->location = $request->input('location');
        $store->phone = $request->input('phone');
        $store->url_map = $request->input('url_map');
        $store->status = $request->input('status');
        $store->is_bann = $request->input('is_bann');
        $store->bann_msg = $request->input('bann_msg');
        $store->latitude = $request->input('latitude');
        $store->longitude = $request->input('longitude');
        $store->user_id = auth()->user()->id;

        // Update work days and hours
        $workDays = $request->input('work_days');
        $workDayHours = [];
        foreach ($workDays as $day) {
            $workDayHours[$day] = [
                'from' => $request->input($day . '_from'),
                'to' => $request->input($day . '_to'),
            ];
        }
        $store->work_days = json_encode($workDayHours);

        // Update the store image
        if ($request->hasFile('store_image')) {
            // Delete the old profile image (if it exists)
            if ($store->store_image) {
                $oldImagePath = public_path('store_images/' . $store->store_image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Store the new profile image
            $image = $request->file('store_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('FrontEnd/assets/images/store_images'), $imageName);
            $store->photo = $imageName;
        }
        // dd($store);
        $store->save();

        return back()->with('lang', $lang)->with('success', 'Store deleted successfully.');
    }
    public function nearby(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        // Get the user's location from the request (assuming you're passing it from the front end)
        $userLatitude = $request->input('user_latitude');
        $userLongitude = $request->input('user_longitude');

        $websiteManager = WebsiteManager::first();
        $distance = $websiteManager->map_distance;
        $radius = $distance; // Set the radius in kilometers
        $nearbyStores = Store::select('*')
            ->selectRaw(
                '( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) *
            cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) *
            sin( radians( latitude ) ) ) ) AS distance',
                [$userLatitude, $userLongitude, $userLatitude]
            )
            ->having('distance', '<', $radius)
            ->orderBy('distance')
            ->get();

        // Convert distance to a more readable format
        foreach ($nearbyStores as $store) {
            if ($store->distance < 1.0) {
                $store->distance = number_format($store->distance * 1000, 1, '.', '') . ' ' . ($lang === 'ar' ? 'م' : 'm');
            } else {
                $store->distance = number_format($store->distance, 1, '.', '') . ' ' . ($lang === 'ar' ? 'كم' : 'km');
            }
        }

        // Pass the nearby stores to the Blade view
        return view('FrontEnd.profile.nearstores', ['nearbyStores' => $nearbyStores]);
    }
    public function nearbyApi(Request $request)
    {

        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }
        // Get the user's location from the request (assuming you're passing it from the front end)
        $userLatitude = $request->input('user_latitude');
        $userLongitude = $request->input('user_longitude');
        $websiteManager = WebsiteManager::first();
        $distance = $websiteManager->map_distance;
        // Calculate nearby stores (example using Eloquent)
        $radius = $distance; // Set the radius in kilometers
        $nearbyStores = Store::select('*')
            ->selectRaw(
                '( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) *
        cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) *
        sin( radians( latitude ) ) ) ) AS distance',
                [$userLatitude, $userLongitude, $userLatitude]
            )
            ->having('distance', '<', $radius)
            ->where('verifcation', 1)
            ->where('is_bann', 0)
            ->where('is_deleted', 0)
            ->orderBy('distance')
            ->get();


        // Convert distance to a more readable format
        foreach ($nearbyStores as $store) {
            if ($store->distance < 1.0) {
                $store->distance = number_format($store->distance * 1000, 1, '.', '') . ' ' . ($lang === 'ar' ? 'م' : 'm');
            } else {
                $store->distance = number_format($store->distance, 1, '.', '') . ' ' . ($lang === 'ar' ? 'كم' : 'km');
            }
        }
        $filteredStores = $nearbyStores->map(function ($store) {
            $category = $store->category;
            $region = Region::find($store->region);
            // $region = $store->region;

            return [
                'id' => $store->id,
                'name' => $store->name,
                'photo' => $store->photo,
                'distance' => $store->distance,
                'work_days' => $store->work_days,
                'city' => $store->city,
                'region' => $store->region,
                'latitude' => $store->latitude,
                'longitude' => $store->longitude,
                'location' => $store->location,
                'phone' => $store->phone,
                'status' => $store->status,
                'discounts' => $store->discounts->where('discounts_status', 'working')->where('is_deleted', 0),
                'category_name_ar' => optional($category)->category_name_ar,
                'category_name_en' => optional($category)->category_name_en,
                'region_name_ar' => optional($region)->region_ar,
                'region_name_en' => optional($region)->region_en,
                'region_name' => ($region),
                'category_name' => ($category)



            ];
        });





        // Return the nearby stores as JSON response
        // return response()->json(['nearbyStores' => $nearbyStores]);
        return response()->json(['filteredStores' => $filteredStores]);
    }
    public function storeInfoApi(Request $request)
    {
        $storeId = $request->json('id');
        $userLatitude = $request->json('user_latitude');
        $userLongitude = $request->json('user_longitude');
        $lang = $request->json('lang');

        // Check if user_latitude or user_longitude is null
        if ($userLatitude === null || $userLongitude === null) {
            return response()->json(['error' => ($lang === 'ar' ? 'يرجى توفير موقع المستخدم' : 'Please provide user location')], 400);
        }

        $store = Store::with(['Discounts' => function ($query) {
            $query->where('Discounts_status', 'working')->where('is_deleted', 0);
        }])
            ->select('*')
            ->selectRaw(
                '( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) *
            cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) *
            sin( radians( latitude ) ) ) ) AS distance',
                [$userLatitude, $userLongitude, $userLatitude]
            )
            ->orderBy('distance')
            ->find($storeId);

        if (!$store) {
            return response()->json(['error' => ($lang === 'ar' ? 'المتجر غير موجود' : 'Store not found')], 404);
        }

        // Convert distance to a more readable format
        $store->distance = $this->formatDistance($store->distance, $lang);

        // Fetch category name based on the language
        $category = StoreCategory::find($store->category_id);
        $store->category_name_ar = optional($category)->category_name_ar;
        $store->category_name_en = optional($category)->category_name_en;

        // Fetch region name based on the language
        $region = Region::find($store->region);
        $store->region_name_ar = optional($region)->region_ar;
        $store->region_name_en = optional($region)->region_en;

        if ($store->Discounts->isEmpty()) {
            return response()->json(['store' => $store, 'message' => ($lang === 'ar' ? 'لا تتوفر خصومات لهذا المتجر' : 'No discounts available for this store')]);
        }

        return response()->json(['store' => $store]);
    }

    private function formatDistance($distance, $lang)
    {
        if ($distance < 1.0) {
            return number_format($distance * 1000, 0, '.', '') . ' ' . ($lang === 'ar' ? 'م' : 'm');
        } elseif ($distance >= 1000) {
            return number_format($distance, 2, '.', '') . ' ' . ($lang === 'ar' ? 'كم' : 'km');
        } else {
            return number_format($distance, 2, '.', '') . ' ' . ($lang === 'ar' ? 'كم' : 'km');
        }
    }

    public function verify(Request $request, Store $store)
    {
        $lang = $request->input('lang');
        // Perform store verification logic here
        $store->verifcation = 1;
        $store->save();
        // dd($store);
        return redirect()->back()->with('success', 'Store verified successfully.', ['store' => $store->id, 'lang' => $request->input('lang')]);
    }
    public function destroy(Store $store, Request $request)
    {
        // Check if the logged-in user is the owner of the store and is a vendor
        if (Auth::user()->id !== $store->user_id || !Auth::user()->is_vendor) {
            abort(403, 'Unauthorized action.');
        }
        $store->update(['is_deleted' => 1]);
        $lang = $request->input('lang');

        return redirect()->route('Stores.view', ['lang' => $lang])->with('success', 'Store deleted successfully.');
    }
    public function searchByNameApi(Request $request)
    {
        // Decode JSON input if it exists
        $requestData = $request->json()->all();

        $lang = $requestData['lang'] ?? null;
        $searchTerm = $requestData['search_term'] ?? null;

        // Set the application locale based on the 'lang' parameter
        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        // Validate the incoming data
        $validator = Validator::make($requestData, [
            'search_term' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            // Return validation error message in the requested language
            $errorMessage = $lang === 'en' ? 'Please enter store name.' : __('ادخل اسم المتجر ');
            return new JsonResponse(['error' => $errorMessage], 400);
        }

        // Perform the search query
        $stores = Store::where('name', 'like', '%' . $searchTerm . '%')->get();

        // Check if stores were found
        if ($stores->isEmpty()) {
            // Return response in the requested language
            $errorMessage = $lang === 'en' ? 'No store found with this name.' : __('لا يوجد متجر بهذا الاسم');
            return new JsonResponse(['error' => $errorMessage], 404);
        }

        // Convert the results to a more suitable format for API response
        $filteredStores = $stores->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
            ];
        });

        return new JsonResponse(['stores' => $filteredStores]);
    }


    public function filterStoresApi(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        // Get the user's location from the request
        $userLatitude = $request->input('user_latitude');
        $userLongitude = $request->input('user_longitude');

        // Additional parameters for filtering
        $region = $request->input('region');
        $categoryName = $request->input('category');

        // Define the label for "All Regions" based on the language
        $allRegionsLabel = ($lang === 'ar') ? 'جميع المدن' : 'All Regions';
        $allCategoriesLabel = ($lang === 'ar') ? 'جميع الفئات' : 'All Categories';


        // Query to get a list of unique regions and categories
        $regionListQuery = Store::where('verifcation', 1)->distinct('region')->pluck('region')->toArray();

        $regions = Region::whereIn('id', $regionListQuery)->pluck('region_' . $lang, 'id')->toArray();

        // Add "All Regions" to the regions array
        $regions = [0 => $allRegionsLabel] + $regions;

        $regionList = [];

        foreach ($regions as $id => $name) {
            $regionList[] = ['region_id' => $id, 'region_name' => $name];
        }

        // Query to filter stores based on parameters
        $query = Store::select('*');

        if ($region && $region !== $allRegionsLabel) {
            $query->where('region', $region);
        }

        if ($categoryName && $categoryName !== $allCategoriesLabel) {
            // Convert category name to ID

            $query->where('category_id', $categoryName);
        }
        $query->where('verifcation', 1)
            ->where('is_bann', 0)
            ->where('is_deleted', 0);
        // Add distance calculation if user latitude and longitude are provided
        if ($userLatitude !== null && $userLongitude !== null) {
            $query->selectRaw(
                '( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) *
                cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) *
                sin( radians( latitude ) ) ) ) AS distance',
                [$userLatitude, $userLongitude, $userLatitude]
            )->orderBy('distance');
        }

        // Execute the query
        $filteredStores = $query->get();

        // Convert distance to a more readable format
        foreach ($filteredStores as $store) {
            $store->distance = $this->formatDistance($store->distance, $lang);
        }

        // Get category names based on the selected region

        if ($region === null || $region == 0) {
            // If region is null or 0, get category list query without filtering by region
            $categoryListQuery = Store::where('verifcation', 1)->distinct('category_id')->pluck('category_id');
        } else {
            // If region is provided, filter category list query by region
            $categoryListQuery = Store::where('verifcation', 1)->distinct('category_id')->where('region', $region)->pluck('category_id');
        }
        $categories = StoreCategory::whereIn('id', $categoryListQuery)
            ->pluck('category_name_' . $lang, 'id')
            ->toArray();


        // Add "All Categories" to the categories array
        $categories = [0 => $allCategoriesLabel] + $categories;

        $categoryList = [];

        foreach ($categories as $id => $name) {
            $categoryList[] = ['category_id' => $id, 'category_name' => $name];
        }

        // Map the stores to the desired format
        $filteredStores = $filteredStores->map(function ($store) use ($lang) {
            $category = StoreCategory::find($store->category_id);
            $regionName = Region::where('id', $store->region)->value('region_' .  $lang);
            $category = $store->category;
            $region = Region::find($store->region);
            return [
                'id' => $store->id,
                'user_id' => $store->user_id,
                'name' => $store->name,
                'location' => $store->location,
                'phone' => $store->phone,
                'url_map' => $store->url_map,
                'photo' => $store->photo,
                'qr' => $store->qr,
                'total_payments' => $store->total_payments,
                'total_withdrawals' => $store->total_withdrawals,
                'count_times' => $store->count_times,
                'work_hours' => $store->work_hours,
                'work_days' => $store->work_days,
                'city' => $store->city,
                'region' => $store->region,
                'status' => $store->status,
                'verifcation' => $store->verifcation,
                'is_bann' => $store->is_bann,
                'bann_msg' => $store->bann_msg,
                'is_deleted' => $store->is_deleted,
                'created_at' => $store->created_at,
                'updated_at' => $store->updated_at,
                'latitude' => $store->latitude,
                'longitude' => $store->longitude,
                'category_name' => optional($category)->{"category_name_" . $lang}, // Get category name based on language
                'category_id' => $store->category_id, // Get category name based on language
                'distance' => $store->distance,
                'region_name' => $regionName,
                'discounts' => $store->discounts->where('discounts_status', 'working')->where('is_deleted', 0),
                'category_name_ar' => optional($category)->category_name_ar,
                'category_name_en' => optional($category)->category_name_en,
                'region_name_ar' => optional($region)->region_ar,
                'region_name_en' => optional($region)->region_en,

            ];
        });

        // Include the 'region' field, 'regionList', 'category' field, 'categoryList', and 'filteredStores' in the response
        $response = [
            'filteredStores' => $filteredStores,
            'region' => $region,
            'regionList' => $regionList,
            'category' => $categoryName,
            'categoryList' => $categoryList,
        ];

        // Return the response as JSON
        return response()->json($response);
    }

    public function userStores(Request $request)
    {
        $userId = auth()->id();
        // Retrieve stores associated with the authenticated user
        $userStores = Store::where('user_id', $userId)->get();

        // Count verified stores where verification = 1 and is_bann = 0 and is_deleted = 0
        $verifiedStoresCount = Store::where('user_id', auth()->id())
            ->where('verifcation', 1)
            ->where('is_bann', 0)
            ->where('is_deleted', 0)
            ->count();

        // Count pending stores where verification = 0 and is_bann = 0 and is_deleted = 0
        $pendingStoresCount = Store::where('user_id', auth()->id())
            ->where('verifcation', 0)
            ->where('is_bann', 0)
            ->where('is_deleted', 0)
            ->count();

        // Sum of count_times for verified stores
        $sumCountTimes = Store::where('user_id', auth()->id())
            ->where('verifcation', 1)
            ->where('is_bann', 0)
            ->where('is_deleted', 0)
            ->sum('count_times');

        // Sum of total_payments for verified stores
        $sumTotalPayments = Store::where('user_id', auth()->id())
            ->where('verifcation', 1)
            ->where('is_bann', 0)
            ->where('is_deleted', 0)
            ->sum('total_payments');
        // Add category_name_en, category_name_ar, region_name_ar, and region_name_en to each store object
        $userStoresWithDetails = $userStores->map(function ($store) {
            $category = StoreCategory::find($store->category_id);
            $region = Region::find($store->region);

            $store->category_name_en = optional($category)->category_name_en;
            $store->category_name_ar = optional($category)->category_name_ar;
            $store->region_name_ar = optional($region)->region_ar;
            $store->region_name_en = optional($region)->region_en;

            return $store;
        });
        // Return the user's stores along with additional counts and sums
        return response()->json([
            'userStores' => $userStoresWithDetails,
            'verifiedStoresCount' => $verifiedStoresCount,
            'pendingStoresCount' => $pendingStoresCount,
            'sumCountTimes' => $sumCountTimes,
            'sumTotalPayments' => $sumTotalPayments,
        ]);
    }


    /**
     * Create a new store.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createStore(Request $request)
    {
        $lang = $request->input('lang');

        // Check the language and set the appropriate error message
        if ($lang === 'ar') {
            $errorMessages = [
                'name.required' => 'يجب إدخال اسم المتجر',
                'phone.unique' => 'رقم الجوال مستخدم بالفعل. يرجى اختيار رقم آخر.',
                'phone.required' => 'رقم الجوال مطلوب.',
                'location.required' => 'يجب إدخال العنوان بتفاصيله.',
                'status.required' => 'يجب اختيار حالة المتجر.',
                'url_map.required' => 'يجب تحديد المتجر على الخريطة.',
                'work_days.required' => 'يجب تحديد أيام وأوقات العمل.',
                'category_id.required' => 'يجب اختيار الفئة.',
                'tax_number.required' => 'يجب إدخال رقم الضريبة.',
                'tax_number.max' => 'يجب أن يكون رقم الضريبة أقل من :max حرف.',
            ];
            $successMessage = '.تم ارسال طلب متجر جديد بنجاح, سوف يتم الموافقة عليه بعد المراجعة ';
        } else {
            // Default to English
            $errorMessages = [
                'name.required' => 'The store name field is required.',
                'phone.unique' => 'The mobile number is already in use. Please choose a different one.',
                'phone.required' => 'The mobile field is required.',
                'location.required' => 'The address field is required.',
                'status.required' => 'Store status field is required.',
                'url_map.required' => 'Store map field is required.',
                'work_days.required' => 'Works time and days are required.',
                'category_id.required' => 'The category field is required.',
                'tax_number.required' => 'The tax number field is required.',
                'tax_number.max' => 'The tax number must be less than :max characters.',
            ];
            $successMessage = 'new store request has been sent successfully. It will be approved after review.';
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'location' => 'required|max:191',
            'phone' => 'required|max:10|min:10|unique:users,mobile|unique:stores,phone',
            'region' => 'required|string|max:255',
            'photo' => 'nullable',
            'status' => 'required|boolean',
            'category_id' => 'required|integer',
            'tax_number' => 'required|string|max:255',
        ], $errorMessages);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorResponse = [];

            foreach ($errors->keys() as $key) {
                $errorResponse[$key] = $errors->first($key);
            }

            return response()->json(['status' => 'error', 'errors' => $errorResponse], 422);
        }

        $user = auth()->user();

        // Create a new store record
        $store = new Store;
        $store->name = $request->input('name');
        $store->location = $request->input('location');
        $store->phone = $request->input('phone');
        $store->photo = $request->input('photo');
        $store->status = $request->input('status');
        $store->region = $request->input('region');
        $store->latitude = $request->input('latitude');
        $store->longitude = $request->input('longitude');
        $store->category_id  = $request->input('category_id');
        $store->tax_number  = $request->input('tax_number');
        $store->user_id = $user->id;

        // Handle store image upload
        if ($request->hasFile('photo')) {
            $this->handleStoreImageUpload($store, $request->file('photo'));
        } else {
            // If no image is uploaded, set a default image
            $store->photo = 'null-market.png'; // Change 'null-market.png' to your default image filename
        }

        // Save the selected work days and their working hours
        $workDays = $request->input('work_days');

        $store->work_days = $workDays;

        $store->save();
        $this->generateQrCode($store->id);

        // Return a JSON response indicating successful store creation
        return response()->json([
            'status' => 'success',
            'message' => $successMessage,
            'store' => $store,
        ]);
    }



    public function deleteStore(Request $request)
    {
        // Validate the incoming JSON data
        $validator = Validator::make($request->all(), [
            'storeId' => 'required|integer|exists:stores,id',
            'lang' => 'required|in:en,ar', // Language validation
        ]);

        // If the validation fails, return the error response
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        // Retrieve the store ID and language from the request
        $storeId = $request->input('storeId');
        $lang = $request->input('lang');

        // Find the store by ID
        $store = Store::find($storeId);
        $userId = Auth::id();
        // Check if the authenticated user owns the store
        if ($store->user_id !== $userId) {
            return response()->json([
                'error' => ($lang === 'ar' ? 'غير مصرح لك بحذف هذا المتجر' : 'You are not authorized to delete this store')
            ], 403);
        }

        // Set the 'is_deleted' field to 1
        $store->is_deleted = 1;
        $store->save();

        // Determine the appropriate response message based on the language
        $message = ($lang === 'ar') ? 'تم حذف المتجر بنجاح' : 'Store deleted successfully';

        // Return a success response with the appropriate message
        return response()->json(['message' => $message]);
    }
}
