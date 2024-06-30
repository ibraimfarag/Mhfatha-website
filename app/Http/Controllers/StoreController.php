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
use App\Models\TermsAndConditionsPolicy;
use App\Models\City;
use Illuminate\Validation\ValidationException;
use App\Models\Discount;
use App\Models\Request as StoreRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use  App\Models\UserDiscount;

class MaxUnique implements Rule
{
    protected $field;
    protected $maxCount;
    protected $lang;


    public function __construct($field, $maxCount, $lang)
    {
        $this->field = $field;
        $this->maxCount = $maxCount;
        $this->lang = $lang;
    }

    public function passes($attribute, $value)
    {
        $count = Store::where($this->field, $value)->count();

        return $count < $this->maxCount;
    }

    public function message()
    {
        if ($this->lang === 'ar') {
            return "هذا الرقم قد وصل إلى الحد الأقصى المسموح به من الاستخدام $this->maxCount مرات، يرجى اختيار رقم آخر.";
        }

        return "This number has already reached the maximum allowed $this->maxCount times, please choose another number.";
    }
}

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
            if ($store->verifcation === 0) {
                $message = $lang === 'ar' ? 'جاري التحقق من المتجر.' : 'Store verification is pending.';
                return response()->json(['error' => $message], 404);
            }

            // Check if the store is banned
            if ($store->is_bann === 1) {
                $message = $lang === 'ar' ? 'هذا المتجر مغلق مؤقتًا من جهة الادارة.' : 'This store is temporarily closed by management.';
                return response()->json(['error' => $message], 404);
            }
            if ($store->is_deleted === 1) {
                $message = $lang === 'ar' ? 'هذا المتجر غير متاح ' : 'This store is not available.';
                return response()->json(['error' => $message], 404);
            }

            // Check store status
            if ($store->status === 0) {
                $message = $lang === 'ar' ? 'هذا المتجر مغلق مؤقتًا من قبل التاجر.' : 'This store is temporarily closed by the merchant.';
                return response()->json(['error' => $message], 404);
            }
            $discounts = $store->Discounts->where('discounts_status', 'working')->where('is_deleted', 0);

            // Check discount attributes
            if ($discounts->isNotEmpty()) {
                return response()->json(['discounts' => $discounts->all(), 'store' => $store]);
            } else {
                $message = $lang === 'ar' ? 'لا توجد خصومات متاحة.' : 'No discount available.';
                return response()->json(['error' => $message, 'store' => $store], 404);
            }
        } catch (\Exception $e) {
            // Handle decryption errors
            $errorMessage = $lang === 'ar' ? 'فشل في التعرف على المتجر.' : 'Failed to recognize the store.';
            return response()->json(['error' => $errorMessage], 404);
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
    public function downloadMergedImage($storeId)
    {
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

        // // Store the new store image
        // $imageName = time() . '.' . $image->getClientOriginalExtension();
        // $image->move(public_path('FrontEnd/assets/images/store_images'), $imageName);
        // $store->photo = $imageName;
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
        $userStores = Store::where('user_id', $userId)->where('is_deleted', 0)->get();
        $commission = WebsiteManager::first()->commission;

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

            $sumTotalPaymentss = UserDiscount::where('store_id', $store->id)

            ->sum('after_discount');

            $sumCountTimess = UserDiscount::where('store_id', $store->id)->count();


            $depit = UserDiscount::where('store_id', $store->id)
                ->where('obtained_status', 0)
                ->sum('obtained');

            $discounts = UserDiscount::where('store_id', $store->id)->get();
            $storeDetails = [
                'months' => [],
                'total_after_discount' => 0,
                'discount_count' => 0
            ];
    
            foreach ($discounts as $discount) {
                $discountCategory = Discount::find($discount->discount_id);

                $monthYear = $discount->created_at->format('F Y');
                if (!isset($storeDetails['months'][$monthYear])) {
                    $storeDetails['months'][$monthYear] = [
                        'discounts' => [],
                        'total_after_discount' => 0,
                        'discount_count' => 0
                    ];
                }
                
                $discountDetail = array_merge($discount->toArray(), [
                    'discount_category_name' => optional($discountCategory)->category,
                    'discount_percent' => optional($discountCategory)->percent,
                    'discount_start_date' => optional($discountCategory)->start_date,
                    'discount_end_date' => optional($discountCategory)->end_date
                ]);
                $storeDetails['months'][$monthYear]['discounts'][] = $discountDetail;
                $storeDetails['months'][$monthYear]['total_after_discount'] += $discount->after_discount;
                $storeDetails['months'][$monthYear]['discount_count'] += 1;
    
                // Sum the overall total_after_discount and count
                $storeDetails['total_after_discount'] += $discount->after_discount;
                $storeDetails['discount_count'] += 1;
    
            }

            $store->category_name_en = optional($category)->category_name_en;
            $store->category_name_ar = optional($category)->category_name_ar;
            $store->region_name_ar = optional($region)->region_ar;
            $store->region_name_en = optional($region)->region_en;
            $store->depit = $depit;
            $store->storeDetails = $storeDetails;
            $store->sumTotalPayments = $sumTotalPaymentss;
            $store->sumCountTimes = $sumCountTimess;

            return $store;
        });
        // Return the user's stores along with additional counts and sums
        return response()->json([
            'userStores' => $userStoresWithDetails,
            'verifiedStoresCount' => $verifiedStoresCount,
            'pendingStoresCount' => $pendingStoresCount,
            'sumCountTimes' => $sumCountTimes,
            'sumTotalPayments' => $sumTotalPayments,
            'commission' => $commission,
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
                'tax_number.required' => ' يجب إدخال رقم السجل التجاري .',
                'tax_number.max' => 'يجب أن يكون رقم السجل التجاري أقل من :max حرف.',
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
                'tax_number.required' => 'The commercial register field is required.',
                'tax_number.max' => 'The commercial register must be less than :max characters.',
            ];
            $successMessage = 'new store request has been sent successfully. It will be approved after review.';
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'location' => 'required|max:191',
            'phone' => ['required', 'max:10', 'min:10', new MaxUnique('phone', 5, $lang)], // Using custom validation rule
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
        // $store->photo = $request->input('photo');
        $store->status = $request->input('status');
        $store->region = $request->input('region');
        $store->latitude = $request->input('latitude');
        $store->longitude = $request->input('longitude');
        $store->category_id  = $request->input('category_id');
        $store->tax_number  = $request->input('tax_number');
        $store->user_id = $user->id;




        // Store the new store image
        // $imageName = time() . '.' . $request->file('photo')->getClientOriginalExtension();
        // $request->file('photo')->move(public_path('FrontEnd/assets/images/store_images'), $imageName);
        // $store->photo = $imageName;




        // Handle store image upload
        if ($request->hasFile('photo')) {
            // $this->handleStoreImageUpload($store, $request->file('photo'));

            $imageName = time() . '.' . $request->file('photo')->getClientOriginalExtension();
            $request->file('photo')->move(public_path('FrontEnd/assets/images/store_images'), $imageName);
            $store->photo = $imageName;
        } else {
            // If no image is uploaded, set a default image
            $store->photo = 'null-market.png'; // Change 'null-market.png' to your default image filename
            $imageName = 'null-market.png';
        }
        $regionId = $request->input('region');
        $regionNameAR = Region::find($regionId)->region_ar;
        $regionNameEn = Region::find($regionId)->region_en;

        $categoryId = $request->input('category_id');
        $categoryNameAr = StoreCategory::find($categoryId)->category_name_ar;
        $categoryNameEn = StoreCategory::find($categoryId)->category_name_en;

        // Save the selected work days and their working hours
        $workDays = $request->input('work_days');

        $store->work_days = $workDays;

        $store->save();
        $this->generateQrCode($store->id);


        // Insert a row into the requests table
        $requestData = [
            'user_id' => $user->id,
            'store_id' => $store->id,
            'type' => 'create_store',
            'data' => json_encode([
                'name' => $request->input('name'),
                'photo' => $imageName,
                'work_days' => $request->input('work_days'),
                'latitude' => $request->input('latitude'),
                'longitude' => $request->input('longitude'),
                'tax_number' => $request->input('tax_number'),
                'category_id' => $request->input('category_id'),
                'region' => $request->input('region'),
                'region_name_ar' => $regionNameAR,
                'region_name_en' => $regionNameEn,
                'category_name_ar' => $categoryNameAr,
                'category_name_en' => $categoryNameEn,
                'location' => $request->input('location'),

            ]),
            'approved' => false, // Assuming the request is initially not approved
        ];
        StoreRequest::create($requestData);
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
        $pendingRequests = StoreRequest::where('store_id', $storeId)
            ->where('approved', '0')
            ->exists();

        if ($pendingRequests) {
            return response()->json([
                'status' => 'error',
                'message' => ($lang === 'ar') ? 'نآسف، لا يزال طلبك السابق قيد الانتظار.' : 'Sorry, your last  request is still pending approval.',
            ], 422);
        }

        // Set the 'is_deleted' field to 1
        // $store->is_deleted = 1;
        // $store->save();
        $requestData = [
            'user_id' => $userId,
            'store_id' => $storeId,
            'type' => 'delete_store',
            'data' => null, // No additional data needed for store deletion
            'approved' => false, // Assuming the request is initially not approved
        ];
        StoreRequest::create($requestData);
        // Determine the appropriate response message based on the language
        $message = ($lang === 'ar') ? 'تم ارسال طلب حذف المتجر بنجاح' : 'Store deleted request sent successfully';

        // Return a success response with the appropriate message
        return response()->json(['message' => $message]);
    }
    public function MergedImageQr(Request $request)
    {
        $storeId = $request->input('storeId');

        // Get the store information (adjust the logic to fit your needs)
        $store = Store::find($storeId);

        // Paths
        $backgroundPath = public_path('FrontEnd/assets/images/banner/background.png');
        $qrCodePath = public_path('FrontEnd/assets/images/stores_qr/') . $store->qr;
        $outputPath = public_path('FrontEnd/assets/images/stores_qr_banar/merged_image_' . $storeId . '.png'); // Dynamic output path

        // Check if both images exist
        if (!file_exists($backgroundPath) || !file_exists($qrCodePath)) {
            return response()->json(['error' => 'Image not found'], 404);
        }

        // Merge images using $this->mergeImages
        $this->mergeImages($backgroundPath, $qrCodePath, $outputPath, 355, ['x' => 469, 'y' => 2505]);

        // Generate the URL for the merged image
        $mergedImageUrl = url('/FrontEnd/assets/images/stores_qr_banar/merged_image_' . $storeId . '.png');

        return response()->json(['url' => $mergedImageUrl, 'Content-Type' => 'image/png']);
    }
    public function getDiscountsByStoreId(Request $request)
    {
        // Validate the incoming JSON data
        $validator = Validator::make($request->all(), [
            'storeId' => 'required|integer|exists:stores,id',
        ]);

        // If the validation fails, return the error response
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        // Retrieve the store ID from the request
        $storeId = $request->input('storeId');

        // Retrieve the discounts associated with the provided store ID
        $discounts = Discount::where('store_id', $storeId)
            ->where('discounts_status', 'end')
            ->where('is_deleted', 0)
            ->get();

        // Return the discounts in JSON format
        return response()->json(['discounts' => $discounts]);
    }
    public function createDiscount(Request $request)
    {
        // Determine the language
        $lang = $request->input('lang');

        // Define language-specific error messages
        $errorMessages = [
            'store_id.required' => ($lang === 'ar') ? 'معرف المتجر مطلوب.' : 'Store ID is required.',
            'store_id.integer' => ($lang === 'ar') ? 'معرف المتجر يجب أن يكون عددًا صحيحًا.' : 'Store ID must be an integer.',
            'store_id.exists' => ($lang === 'ar') ? 'معرف المتجر غير موجود.' : 'Store ID does not exist.',
            'percent.required' => ($lang === 'ar') ? 'نسبة الخصم مطلوبة.' : 'Discount percentage is required.',
            'percent.numeric' => ($lang === 'ar') ? 'نسبة الخصم يجب أن تكون قيمة رقمية.' : 'Discount percentage must be a numeric value.',
            'percent.min' => ($lang === 'ar') ? 'نسبة الخصم يجب أن لا تقل عن 1.' : 'Discount percentage must not be less than 1.',
            'percent.max' => ($lang === 'ar') ? 'نسبة الخصم يجب أن لا تزيد عن 100.' : 'Discount percentage must not exceed 100.',
            'category.required' => ($lang === 'ar') ? 'الفئة مطلوبة.' : 'Category is required.',
            'category.string' => ($lang === 'ar') ? 'الفئة يجب أن تكون نصًا.' : 'Category must be a string.',
            'category.max' => ($lang === 'ar') ? 'الفئة يجب أن لا تتجاوز :max حرفًا.' : 'Category must not exceed :max characters.',
            'start_date.required' => ($lang === 'ar') ? 'تاريخ بداية الخصم مطلوب.' : 'Start date of the discount is required.',
            'start_date.date' => ($lang === 'ar') ? 'تاريخ بداية الخصم يجب أن يكون تاريخًا صالحًا.' : 'Start date of the discount must be a valid date.',
            'start_date.after_or_equal' => ($lang === 'ar') ? 'تاريخ بداية الخصم يجب أن يكون من اليوم أو بعد ذلك.' : 'Start date of the discount must be from today or a future date.',
            'end_date.required' => ($lang === 'ar') ? 'تاريخ نهاية الخصم مطلوب.' : 'End date of the discount is required.',
            'end_date.date' => ($lang === 'ar') ? 'تاريخ نهاية الخصم يجب أن يكون تاريخًا صالحًا.' : 'End date of the discount must be a valid date.',
            'end_date.after' => ($lang === 'ar') ? 'تاريخ نهاية الخصم يجب أن يكون بعد تاريخ البداية.' : 'End date of the discount must be after the start date.',
            'discounts_status.required' => ($lang === 'ar') ? 'حالة الخصم مطلوبة.' : 'Discount status is required.',
            'discounts_status.in' => ($lang === 'ar') ? 'حالة الخصم يجب أن تكون "start" أو "end" فقط.' : 'Discount status must be either "start" or "end".',
            'lang.required' => ($lang === 'ar') ? 'حقل اللغة مطلوب.' : 'Language field is required.',
            'lang.in' => ($lang === 'ar') ? 'يجب أن يكون حقل اللغة إما "en" أو "ar".' : 'Language field must be either "en" or "ar".',
        ];


        // Validate the incoming JSON data with language-specific error messages
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|integer|exists:stores,id',
            'percent' => 'required|numeric|min:1|max:100',
            'category' => 'required|string|max:255',
            'start_date' => [
                'required',
                'date',
                // Check if start_date is after or equal to today's date
                function ($attribute, $value, $fail) {
                    if (Carbon::parse($value)->isBefore(Carbon::today())) {
                        $fail(__('The :attribute must start from today or a future date.', ['attribute' => __('Start Date')]));
                    }
                },
            ],
            'end_date' => 'required|date|after:start_date',
            'discounts_status' => 'required|in:start,end',
            'lang' => 'required|in:en,ar',
        ], $errorMessages);

        // If the validation fails, return the error response with language-specific messages
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }

        // Create a new discount instance and save it to the database
        // (same as the previous implementation)

        // Return a success response with the created discount details
        return response()->json([
            'message' => ($lang === 'ar') ? 'تم إنشاء الخصم بنجاح.' : 'Discount created successfully.',
        ]);
    }

    public function DeleteDiscount(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'discount_id' => 'required|exists:discounts,id',
            'lang' => 'required|in:en,ar',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status' => 'error', 'errors' => $errors], 422);
        }

        $discountId = $request->input('discount_id');
        $lang = $request->input('lang');

        // Retrieve the discount by ID
        $discount = Discount::find($discountId);
        // Check if the discount exists
        if (!$discount) {
            return response()->json([
                'status' => 'error',
                'message' => ($lang === 'ar') ? 'الخصم غير موجود.' : 'Discount does not exist.',
            ], 404);
        }

        // Create a request to be approved
        // Create request data for deleting the discount
        // Create request data for deleting the discount
        $requestData = [
            'user_id' => auth()->id(), // ID of the user making the request
            'store_id' => $discount->store_id, // ID of the store associated with the discount
            'type' => 'delete_discount', // Type of the request
            'data' => json_encode([
                'discount_id' => $discountId, // ID of the discount being deleted
                'is_deleted' => 1, // Indicate that the discount is to be deleted
            ]),
            'approved' => false, // Initially set to false as it needs approval
        ];

        // Add the request to the requests table
        $newRequest = StoreRequest::create($requestData);

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => ($lang === 'ar') ? 'تم إرسال طلب الغاء الخصم بنجاح.' : 'cancel request sent successfully.',
            'request_id' => $newRequest->id,
        ]);
    }

    public function updateStore(Request $request)
    {

        $storeId = $request->input('store_id');
        $lang = $request->input('lang');
        $store = Store::find($storeId);
        // Check if there are pending update requests for the store
        $pendingRequests = StoreRequest::where('store_id', $storeId)
            ->where('approved', '0')
            ->exists();


        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|max:191',
            'location' => 'required|max:191',
            'phone' => ['required', 'max:10', 'min:10', new MaxUnique('phone', 5, $lang)], // Using custom validation rule

            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust the file size and allowed types as needed
            'work_days' => 'required', // Assuming work_days is an array
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'tax_number' => 'nullable|max:20',
            'category_id' => 'nullable|exists:store_categories,id',
            'region' => 'nullable|max:191',
            'lang' => 'required|in:en,ar',
        ], [
            // Custom error messages based on language
            'store_id.required' => ($request->input('lang') === 'ar') ? 'معرف المتجر مطلوب.' : 'Store ID is required.',
            'store_id.exists' => ($request->input('lang') === 'ar') ? 'معرف المتجر غير صالح.' : 'Invalid store ID.',
            'name.required' => ($request->input('lang') === 'ar') ? ' اسم المتجر مطلوب.' : 'The store name field is required.',
            'name.max' => ($request->input('lang') === 'ar') ? 'يجب أن يكون اسم المتجر أقل من :max حرف.' : 'The store name must be less than :max characters.',
            'location.required' => ($request->input('lang') === 'ar') ? ' العنوان مطلوب.' : 'The address field is required.',
            'photo.required' => ($request->input('lang') === 'ar') ? ' الصورة مطلوب.' : 'The photo field is required.',
            'photo.image' => ($request->input('lang') === 'ar') ? 'يجب أن يكون الملف نوع صورة.' : 'The file must be an image.',
            'photo.mimes' => ($request->input('lang') === 'ar') ? 'يجب أن يكون نوع الملف jpeg أو png أو jpg أو gif فقط.' : 'The file type must be jpeg, png, jpg, or gif only.',
            'photo.max' => ($request->input('lang') === 'ar') ? 'يجب أن يكون حجم الملف أقل من :max كيلوبايت.' : 'The file size must be less than :max kilobytes.',
            'work_days.required' => ($request->input('lang') === 'ar') ? ' أيام العمل مطلوب.' : 'The work days field is required.',
            'latitude.required' => ($request->input('lang') === 'ar') ? ' خط العرض مطلوب.' : 'The latitude field is required.',
            'latitude.numeric' => ($request->input('lang') === 'ar') ? 'يجب أن يكون خط العرض رقمًا.' : 'The latitude must be a number.',
            'longitude.required' => ($request->input('lang') === 'ar') ? ' خط الطول مطلوب.' : 'The longitude field is required.',
            'longitude.numeric' => ($request->input('lang') === 'ar') ? 'يجب أن يكون خط الطول رقمًا.' : 'The longitude must be a number.',
            'tax_number.max' => ($request->input('lang') === 'ar') ? 'يجب أن يكون رقم السجل التجاري أقل من :max حرف.' : 'The tax number must be less than :max characters.',
            'category_id.exists' => ($request->input('lang') === 'ar') ? 'معرف التصنيف غير صالح.' : 'Invalid category ID.',
            'region.max' => ($request->input('lang') === 'ar') ? 'يجب أن يكون الإقليم أقل من :max حرف.' : 'The region must be less than :max characters.',
        ]);


        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status' => 'error', 'errors' => $errors], 422);
        }


        // Check if the store exists
        if (!$store) {
            return response()->json([
                'status' => 'error',
                'message' => ($lang === 'ar') ? 'المتجر غير موجود.' : 'Store not found.',
            ], 404);
        }

        // Check if any relevant field has changed
        $nameChanged = $request->filled('name') && $store->name !== $request->input('name');
        $photoChanged = $request->hasFile('photo');
        $taxNumberChanged = $request->filled('tax_number') && $store->tax_number !== $request->input('tax_number');
        $mobileNumberChanged = $request->filled('phone') && $store->tax_number !== $request->input('phone');

        $categoryIdChanged = $request->filled('category_id') && $store->category_id !== $request->input('category_id');
        $regionChanged = $request->filled('region') && $store->region !== $request->input('region');
        $regionId = $request->input('region');
        $regionNameAR = Region::find($regionId)->region_ar;
        $regionNameEn = Region::find($regionId)->region_en;

        $categoryId = $request->input('category_id');
        $categoryNameAr = StoreCategory::find($categoryId)->category_name_ar;
        $categoryNameEn = StoreCategory::find($categoryId)->category_name_en;

        // If any relevant field has changed, create a request for approval
        if ($nameChanged || $photoChanged || $taxNumberChanged || $categoryIdChanged || $regionChanged || $mobileNumberChanged) {

            // Delete the old image if it exists
            if ($store->photo) {
                $oldImagePath = public_path('store_images/' . $store->photo);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Store the new store image
            if ($request->hasFile('photo')) {
                $imageName = time() . '.' . $request->file('photo')->getClientOriginalExtension();
                $request->file('photo')->move(public_path('FrontEnd/assets/images/store_images'), $imageName);
                $store->photo = $imageName;
            } else {
                $imageName = $store->photo; // Keep the old image if no new one is uploaded
            }


            $requestData = [
                'user_id' => auth()->id(),
                'store_id' => $storeId,
                'type' => 'update_store',
                'data' => json_encode([
                    'name' => $request->input('name'),
                    'photo' => $imageName, // Use the newly uploaded image name here
                    'work_days' => $request->input('work_days'),
                    'latitude' => $request->input('latitude'),
                    'longitude' => $request->input('longitude'),
                    'tax_number' => $request->input('tax_number'),
                    'phone' => $request->input('phone'),
                    'category_id' => $request->input('category_id'),
                    'region' => $request->input('region'),
                    'region_name_ar' => $regionNameAR,
                    'region_name_en' => $regionNameEn,
                    'category_name_ar' => $categoryNameAr,
                    'category_name_en' => $categoryNameEn,
                ]),
                'approved' => false, // Set to false initially as it needs approval
            ];
            if ($pendingRequests) {
                return response()->json([
                    'status' => 'error',
                    'message' => ($lang === 'ar') ? 'نآسف، لا يزال طلبك السابق قيد الانتظار.' : 'Sorry, your last  request is still pending approval.',
                ], 422);
            }
            // Add the request to the requests table
            $newRequest = StoreRequest::create($requestData);

            // Return a response indicating that a request has been sent for approval
            return response()->json([
                'status' => 'success',
                'message' => ($lang === 'ar') ? 'تم إرسال طلب التحديث بنجاح.' : 'Update request sent successfully.',
                'request_id' => $newRequest->id,
            ]);
        }


        // Update the store details if no request is needed or after the request is approved
        $store->update([
            'name' => $request->input('name', $store->name),
            'location' => $request->input('location', $store->location),
            'photo' => $request->hasFile('photo') ? $this->handleStoreImageUpload($store, $request->file('photo')) : $store->photo,
            'work_days' => $request->input('work_days', $store->work_days),
            'latitude' => $request->input('latitude', $store->latitude),
            'longitude' => $request->input('longitude', $store->longitude),
            'tax_number' => $request->input('tax_number', $store->tax_number),
            'category_id' => $request->input('category_id', $store->category_id),
            'region' => $request->input('region', $store->region),
            'phone' => $request->input('region', $store->phone),
        ]);

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => ($lang === 'ar') ? 'تم تحديث بيانات المتجر بنجاح.' : 'Store data updated successfully.',
            'store' => $store,
        ]);
    }

    public function manageStore(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'query' => 'required|in:delete,restore,ban,unban',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status' => 'error', 'errors' => $errors], 422);
        }

        $storeId = $request->input('store_id');
        $query = $request->input('query');

        // Retrieve the store by ID
        $store = Store::find($storeId);

        // Check if the store exists
        if (!$store) {
            return response()->json([
                'status' => 'error',
                'message' => 'Store not found.',
            ], 404);
        }
        dd($store);
        // Perform the operation based on the query
        switch ($query) {
            case 'delete':
                $store->is_deleted = 1;
                $message = 'Store deleted successfully.';
                break;
            case 'restore':
                $store->is_deleted = 0;
                $message = 'Store restored successfully.';
                break;
            case 'ban':
                $store->is_bann = 1;
                $message = 'Store banned successfully.';
                break;
            case 'unban':
                $store->is_bann = 0;
                $message = 'Store unbanned successfully.';
                break;
            default:
                // This case should never be reached if validation is done properly
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid query.',
                ], 400);
        }
        $store->save();

        // Return a success response
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }
}
