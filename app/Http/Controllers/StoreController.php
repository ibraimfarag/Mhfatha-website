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
    public function decryptQrCode($encryptedStoreID)
{
    // Call the decryption function
    $decryptedStoreID = $this->decryptQrCode($encryptedStoreID);

    // Return the decrypted store ID or an appropriate response
    return response()->json(['decryptedStoreID' => $decryptedStoreID]);
}
public function generateQrCode($storeID)
{
    // Convert the numeric store ID to a string
    $storeIDAsString = strval($storeID);

    // Encrypt and encode the store ID
    $encryptedStoreID = base64_encode(encrypt($storeIDAsString));

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
            $store->photo = 'market.png'; // Change 'market.png' to your default image filename
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
        $image->move(public_path('FrontEnd\assets\images\store_images'), $imageName);
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
            $image->move(public_path('FrontEnd\assets\images\store_images'), $imageName);
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

        // Calculate nearby stores (example using Eloquent)
        $radius = 5; // Set the radius in kilometers
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

        // Calculate nearby stores (example using Eloquent)
        $radius = 5; // Set the radius in kilometers
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
        $filteredStores = $nearbyStores->map(function ($store) {
            return [
                'id' => $store->id,
                'name' => $store->name,
                'photo' => $store->photo,
                'distance' => $store->distance,
                'work_days'=> $store->work_days,
                'city'=> $store->city,
                'region'=> $store->region,
                'latitude'=> $store->latitude,
                'longitude'=> $store->longitude,
                'location'=> $store->location,
                'phone'=> $store->phone,
                'status'=> $store->status,
                'discounts' => $store->discounts->where('discounts_status', 'working')->where('is_deleted', 0),

            ];
        });
        
        
        


        // Return the nearby stores as JSON response
        // return response()->json(['nearbyStores' => $nearbyStores]);
        return response()->json(['filteredStores' => $filteredStores]);

    }
    public function storeInfoApi(Request $request)
    {
        $storeId = $request->json('id');

        $store = Store::with(['Discounts' => function ($query) {
            $query->where('Discounts_status', 'working')->where('is_deleted', 0);
        }])->find($storeId);

        if (!$store) {
            return response()->json(['error' => 'Store not found'], 404);
        }

        if ($store->Discounts->isEmpty()) {
            return response()->json(['store' => $store, 'message' => 'No discounts available for this store']);
        }

        return response()->json(['store' => $store]);
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
}
