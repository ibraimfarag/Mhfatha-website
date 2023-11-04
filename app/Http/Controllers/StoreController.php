<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use BaconQrCode\Renderer\Image\Png;
use BaconQrCode\Writer;
use Illuminate\Support\Facades\Validator;
// use BaconQrCode\Renderer\RendererInterface;
use Illuminate\Support\Facades\File;

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
    $query->where(function($q) use ($searchTerm) {
        $q->where('name', 'like', '%' . $searchTerm . '%')
        ->orwhere('phone', 'like', '%' . $searchTerm . '%')
          ->orWhereHas('user', function($u) use ($searchTerm) {
              $u->where('first_name', 'like', '%' . $searchTerm . '%')
                ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                ->orWhere('mobile', 'like', '%' . $searchTerm . '%');
          });
    });
}
if ($request->has('status') && $request->input('status') === null) {
    $query->getQuery()->orders = [];
}
$userStores = $query->get();
} else {
            // User is not an admin, return stores associated with their user ID
            $userStores = Store::where('user_id', $user->id)->where('is_deleted', 0)->get();
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
    
    public function store(Request $request)
    {
        // Define validation rules for the input data
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:191',
            'location' => 'required|max:191',
            'phone' => 'required|max:10|min:10',
            'url_map' => 'nullable',
            'photo' => 'nullable',
            'work_hours' => 'nullable|string',
            'work_days' => 'nullable|array', // Make sure 'work_days' is an array
            'status' => 'boolean',
        ]);
    
        if ($validator->fails()) {
            $lang = $request->input('lang');
            return redirect()->route('Stores.view', ['lang' => $lang])->withErrors($validator);
        }
        $lang = $request->input('lang');
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
        $store->user_id = $user->id;
        

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
    
        return redirect()->route('Stores.view', ['lang' => $lang])->with('success', 'Store Added Successfully.');
    }
    public function edit(Request $request)
    {
        $lang = $request->input('lang');
        $storeid = $request->input('storeid'); // Retrieve the 'storeid' parameter
    
        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        $store = Store::find($storeid);

        return view('FrontEnd.profile.stores.edit',['store' => $store]);
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

    $store->save();

    return back()->with('lang', $lang)->with('success', 'Store deleted successfully.');
}

public function verify(Request $request, Store $store)
{
       $lang = $request->input('lang');
    // Perform store verification logic here
    $store->verifcation = 1;
    $store->save();
// dd($store);
    return redirect()->back()->with('success', 'Store verified successfully.',['store' => $store->id, 'lang' => $request->input('lang')]);
}
    public function destroy(Store $store , Request $request)
    {
        // Check if the logged-in user is the owner of the store and is a vendor
        if (Auth::user()->id !== $store->user_id || !Auth::user()->is_vendor) {
            abort(403, 'Unauthorized action.');
        }
        $store->update(['is_deleted' => 1]);
        $lang = $request->input('lang');

        return redirect()->route('Stores.view',['lang' => $lang])->with('success', 'Store deleted successfully.');
    }
    }
