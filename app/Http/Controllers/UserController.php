<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use  App\Models\User;
use  App\Models\Discount;
use  App\Models\UserDiscount;
use  App\Models\Store;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;

class UserController extends Controller
{
    public function dashboard_user(Request $request)
    {
        $user = Auth::user();
        $is_admin = $user->is_admin; // Assuming you have an 'is_admin' field in your users table to determine admin status
        $is_vendor = $user->is_vendor;

        $lang = $request->input('lang');
    
        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }
    

    
        if ($is_admin) {
            // If the user is an admin, calculate the user discounts count
            $userDiscountsCount = UserDiscount::count();
            $totalAfterDiscount = UserDiscount::sum('after_discount');
    
            $totalRemainingProfit = UserDiscount::where('status', 1)
                ->where('obtained_status', 0)
                ->sum('after_discount');
    
            // Get 1% of the total remaining profit
            $totalRemainingProfit *= 0.01;
    
            $vendorCount = User::where('is_vendor', 1)->count();
            $maleVendorCount = User::where('is_vendor', 1)->where('gender', 'male')->count();
            $femaleVendorCount = User::where('is_vendor', 1)->where('gender', 'female')->count();
            $nonVendorCount = User::where('is_vendor', 0)->count();
            $maleNonVendorCount = User::where('is_vendor', 0)->where('gender', 'male')->count();
            $femaleNonVendorCount = User::where('is_vendor', 0)->where('gender', 'female')->count();
            $storeCount = Store::where('status', 1)->count();
            $currentDiscountsCount = Discount::where('discounts_status', 'working')
                ->where('is_deleted', 0)
                ->count();
        
            $rejectedOrdersCount = UserDiscount::where('status', 2)
                ->where('obtained_status', 0)
                ->count();



                return view('FrontEnd.profile.user-dashboard')
                ->with('user', $user)
                ->with('userDiscountsCount', $userDiscountsCount)
                ->with('is_admin', $is_admin)
                ->with('totalAfterDiscount', $totalAfterDiscount)
                ->with('totalRemainingProfit', $totalRemainingProfit)
                ->with('vendorCount', $vendorCount)
                ->with('maleVendorCount', $maleVendorCount)
                ->with('femaleVendorCount', $femaleVendorCount)
                ->with('nonVendorCount', $nonVendorCount)
                ->with('maleNonVendorCount', $maleNonVendorCount)
                ->with('femaleNonVendorCount', $femaleNonVendorCount)
                ->with('storeCount', $storeCount)
                ->with('currentDiscountsCount', $currentDiscountsCount)
                ->with('rejectedOrdersCount', $rejectedOrdersCount)
        ;
    
        }
        elseif ($is_vendor) {



            $userDiscountsCountForStores = UserDiscount::whereIn('store_id', function ($query) use ($user) {
                $query->select('id')->from('stores')->where('user_id', $user->id);
            })->count();
             // Calculate 1% of the sum of 'after_discount' where 'status' is 1 and 'obtained_status' is 0
    $debitCreditValue = UserDiscount::where('status', 1)
    ->where('obtained_status', 0)
    ->sum('after_discount') * 0.01;
            // Add specific logic for vendors here
            $vendorSpecificData = [
                // Perform actions or fetch data specific to vendors
                'storeCountForUser' => Store::where('user_id', $user->id)
                ->where('is_deleted', 0)
                ->where('verifcation', 1)
                ->where('is_bann', 0)
                ->count(),


                'storeDiscountsCount' => Discount::whereIn('store_id', function ($query) use ($user) {
                    $query->select('id')->from('stores')->where('user_id', $user->id);
                })
                    ->where('discounts_status', 'working')
                    ->where('is_deleted', 0)
                    ->count(),



                    // Add more vendor-specific data as needed
                    'debitCreditValue' => $debitCreditValue,
                    'userDiscountsCountForStores' => $userDiscountsCountForStores,




            ];


            
            // Fetch totalAfterDiscount data for the chart
            $totalAfterDiscountData = $this->getTotalAfterDiscountChartData($user->id);

            return view('FrontEnd.profile.user-dashboard')
                ->with('user', $user)
                ->with('is_vendor', $is_vendor)
                ->with('vendorSpecificData', $vendorSpecificData)
              
                ->with('totalAfterDiscountData', json_encode($totalAfterDiscountData));
        }
        
        return view('FrontEnd.profile.user-dashboard')
        ->with('user', $user)
        ->with('is_admin', $is_admin)
       
;
       
    }
    
    private function getTotalAfterDiscountChartData($userId)
    {
        $data = [];

        // Example: Fetch totalAfterDiscount data for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $formattedDate = $date->format('Y-m-d');

            $totalAfterDiscount = UserDiscount::where('user_id', $userId)
                ->whereDate('created_at', $formattedDate)
                ->sum('after_discount');

            $data[] = [
                'date' => $formattedDate,
                'totalAfterDiscount' => $totalAfterDiscount,
            ];
        }

        return $data;
    }
    
    public function getDiscountChartData($timePeriod)
{
    $user = auth()->user();

    $startDate = Carbon::now();
    $endDate = Carbon::now();

    // Adjust the date range based on the selected time period
    switch ($timePeriod) {
        case 'day':
            $startDate->subDay();
            break;
        case 'week':
            $startDate->subWeek();
            break;
        case 'month':
            $startDate->subMonth();
            break;
        case 'year':
            $startDate->subYear();
            break;
        case 'seven_years':
            $startDate->subYears(7);
            break;
        default:
            // Default to one week
            $startDate->subWeek();
            break;
    }

    // Fetch totalAfterDiscount values for the specified time period
    $discountData = UserDiscount::where('user_id', $user->id)
        ->whereBetween('created_at', [$startDate, $endDate])
        ->pluck('after_discount');

    // Generate labels (e.g., days, weeks, months) based on the selected time period
    $labels = collect(range(0, $discountData->count() - 1))->map(function ($item) use ($timePeriod, $startDate) {
        return $startDate->copy()->add($item, $timePeriod)->format('Y-m-d');
    });

    return response()->json(['success' => true, 'labels' => $labels, 'dataset' => $discountData]);
}

    public function showProfile(Request $request)
{
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }
    $user = Auth::user(); // Assuming you're using Laravel's built-in Auth
    return view('FrontEnd.profile.profile', compact('user'));
}
public function showProfiles(Request $request)
{
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }
    $users = User::all();
    // Assuming you're using Laravel's built-in Auth

    $users->each(function ($user) {
        $user->age = $this->getAge($user->birthday);
    });
    return view('FrontEnd.profile.mangeUser.index', compact('users'));
}

public function fetchUsers(Request $request)
{
    $tags = $request->input('tags');

    // Query the users based on the provided tags
    $users = User::where(function ($query) use ($tags) {
        foreach ($tags as $tag) {
            $query->where('first_name', 'like', "%$tag%")
                ->orWhere('last_name', 'like', "%$tag%")
                ->orWhere('email', 'like', "%$tag%")
                ->orWhere('mobile', 'like', "%$tag%")
                ->orWhere('region', 'like', "%$tag%")
                ->orWhere('city', 'like', "%$tag%")
                ->orWhere('gender', 'like', "%$tag%");

                $age = (int) $tag;
                if ($age > 0) {
                    $query->orWhereRaw('YEAR(CURDATE()) - YEAR(birthday) - (DATE_FORMAT(CURDATE(), "%m%d") < DATE_FORMAT(birthday, "%m%d")) = ?', [$age]);
                }
        }
    })->get();

    return response()->json($users);
}


public function getAge($birthday)
{
    return Carbon::parse($birthday)->age;
}

public function showProfile_user(Request $request )
{
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }
    $userID = $request->input('userid');
    $user = User::where('id', $userID)->first();

    // dd($userID);

    return view('FrontEnd.profile.mangeUser.user-edit',  ['user' => $user]);
}
public function update_profile(Request $request)
{


    $currentLanguage = $request->input('lang');



    // Check the language and set the appropriate error message
    if ($currentLanguage === 'ar') {
        $errorMessages = [
            'first_name.required' => 'حقل الاسم الأول مطلوب.',
            'last_name.required' => 'حقل الاسم الأخير مطلوب.',
            'gender.required' => 'حقل الجنس مطلوب.',
            'birthday.required' => 'حقل تاريخ الميلاد مطلوب.',
            'mobile.unique' => 'رقم الجوال مستخدم بالفعل. يرجى اختيار رقم آخر.',
            'mobile.required' => 'حقل رقم الجوال مطلوب.',
            'email.required' => 'حقل البريد الإلكتروني مطلوب.',
            'email.unique' => 'البريد الالكتروني مستخد بالفعل .',
            'password.required' => 'حقل كلمة المرور مطلوب.',
            'password.min' => 'يجب أن تحتوي كلمة المرور على ما لا يقل عن 8 أحرف.',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق.',
        ];
    }
    if ($currentLanguage === 'en') {
        $errorMessages = [
            'first_name.required' => 'The first name field is required.',
            'last_name.required' => 'The last name field is required.',
            'gender.required' => 'The gender field is required.',
            'birthday.required' => 'The birthday field is required.',
            'mobile.unique' => 'The mobile number is already in use. Please choose a different one.',
            'mobile.required' => 'The mobile field is required.',
            'email.required' => 'The email field is required.',
            'password.required' => 'The password field is required.',
            'password.min' => 'The password must be at least 8 characters.',
            'password.confirmed' => 'The password confirmation does not match.',
        ];
    }


    $customMessages = $errorMessages;

    $lang = $request->input('lang'); 
    // Validate the incoming request data
    $this->validate($request, [
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'birthday' => 'required|date',
        'gender' => 'required|in:male,female',
        'city' => 'required|string|max:255',
        'region' => 'required|string|max:255',
        'mobile' => 'required|string|max:20',
        'email' => 'required|string|email|max:255',
        'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file type and size as needed
    ],$customMessages);
    // Check if the provided mobile number is already in use by another user
    $userWithMobile = User::where('mobile', $request->input('mobile'))->first();

    if ($userWithMobile && $userWithMobile->id !== Auth::user()->id) {
        // Redirect back with an error message
        return redirect()->route('profile', ['lang' => $lang])
            ->with('error', $customMessages)
            ->withInput();
    }

    // Update the user's profile information
    $user = User::where('email', $request->input('email'))->first();
    $user->first_name = $request->input('first_name');
    $user->middle_name = $request->input('middle_name');
    $user->last_name = $request->input('last_name');
    $user->birthday = $request->input('birthday');
    $user->gender = $request->input('gender');
    $user->city = $request->input('city');
    $user->region = $request->input('region');
    $user->mobile = $request->input('mobile');
    $user->email = $request->input('email');

    // Check if a new profile image was uploaded
    if ($request->hasFile('profile_image')) {
        // Delete the old profile image (if it exists)
        if ($user->profile_image) {
            $oldImagePath = public_path('profile_images/' . $user->profile_image);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
        }

        // Store the new profile image
        $image = $request->file('profile_image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('FrontEnd\assets\images\user_images'), $imageName);
        $user->photo = $imageName;
    }

    // Save the updated user data
    $user->save();
    $successMessage = ($currentLanguage === 'ar') ? 'تم تحديث الملف الشخصي بنجاح.' : 'Profile updated successfully.';

    // Redirect back to the profile page or wherever you want
    return redirect()->route('profile', ['lang' => $lang])->with('success', $successMessage);
}



public function updateAdminProfile(Request $request , user $user )

{
    $lang = $request->input('lang'); 

    $userId=$request->input('userid');

    // dd($userId);

    // Validate the incoming request data
    $this->validate($request, [
        'first_name' => 'required|string|max:255',
        'middle_name' => 'nullable|string|max:255',
        'last_name' => 'required|string|max:255',
        'birthday' => 'required|date',
        'gender' => 'required|in:male,female',
        'city' => 'required|string|max:255',
        'region' => 'required|string|max:255',
        'mobile' => 'required|string|max:20',
        'email' => 'required|string|email|max:255',
        'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file type and size as needed
    ]);

    // Check if the provided mobile number is already in use by another user
    $userWithMobile = User::where('mobile', $request->input('mobile'))->where('id', '!=', $userId)->first();

    if ($userWithMobile) {
        // Redirect back with an error message
        return redirect()->back()
            ->with('error', 'The mobile number is already in use by another user.')
            ->withInput();
    }

    // Get the user to be updated
    $user = User::find($userId);
   if (!$user) {
        // Handle the case where the user is not found
        return redirect()->back()->with('error', 'User not found');
    }

    // Update the user's profile information
    $user->first_name = $request->input('first_name');
    $user->middle_name = $request->input('middle_name');
    $user->last_name = $request->input('last_name');
    $user->birthday = $request->input('birthday');
    $user->gender = $request->input('gender');
    $user->city = $request->input('city');
    $user->region = $request->input('region');
    $user->mobile = $request->input('mobile');
    $user->email = $request->input('email');

    // Check if a new profile image was uploaded
    if ($request->hasFile('profile_image')) {
        // Delete the old profile image (if it exists)
        if ($user->profile_image) {
            $oldImagePath = public_path('profile_images/' . $user->profile_image);
            if (File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
        }

        // Store the new profile image
        $image = $request->file('profile_image');
        $imageName = time() . '.' . $image->getClientOriginalExtension();
        $image->move(public_path('FrontEnd\assets\images\user_images'), $imageName);
        $user->profile_image = $imageName;
    }
    // Save the updated user data
    $user->save();
    // dd(  $user); 

    // Redirect back to the admin user profile edit page or wherever you want
    return back()->with(['lang' => $lang, 'user' => $user])->with('success', 'User profile updated successfully.');
}


}
