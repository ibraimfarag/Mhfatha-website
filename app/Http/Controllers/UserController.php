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
use Illuminate\Support\Facades\Validator;
use App\Models\Region; // Import the Region model
use App\Models\City;
use App\Models\StoreCategory;
use App\Models\WebsiteManager;
use App\Models\Request as Requests;



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
                ->with('rejectedOrdersCount', $rejectedOrdersCount);
        } elseif ($is_vendor) {



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
            ->with('is_admin', $is_admin);
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

    public function showProfile_user(Request $request)
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
        ], $customMessages);
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

    /**
     * Get or Update user profile through API.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateProfileApi(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date',
            'gender' => 'required|in:male,female',
            'city' => 'required|string|max:255',
            'region' => 'required|string|max:255', // Assuming a region field in the request
            'mobile' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'profile_image' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file type and size as needed
        ]);

        if ($validator->fails()) {
            $userId = Auth::user()->id;
            $user = User::find($userId);
            $regions = Region::select('id', 'region_ar', 'region_en')->get();

            return response()->json(['error' => $validator->errors(), 'user' => $user, 'regions' => $regions], 422);
        }

        // Check if the user exists
        $userId = Auth::user()->id;
        $user = User::find($userId);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Update the user's profile information
        $user->fill($request->all());

        // Check if a new profile image was uploaded
        if ($request->hasFile('profile_image')) {
            // Delete the old profile image (if it exists)
            if ($user->profile_image) {
                $oldImagePath = public_path('FrontEnd/assets/images/user_images/' . $user->profile_image);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Store the new profile image
            $image = $request->file('profile_image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('FrontEnd/assets/images/user_images'), $imageName);
            $user->profile_image = $imageName;
        }
        if ($request->input('mobile') !== $user->mobile) {
            // Generate a random verification code (you can adjust the length as needed)
            $verificationCode = mt_rand(100000, 999999);

            // Store the verification code in the user record
            $user->verification_code = $verificationCode;

            // Save the updated user data
            $user->save();

            // You can send the verification code to the user via SMS or any other method here

            // For demonstration purposes, let's assume you're just returning the code in the response
            return response()->json([
                'message' => 'Mobile number updated. Verification code sent.',
                'verification_code' => $verificationCode,
            ]);
        }

        // Save the updated user data
        $user->save();
        $regionId = $request->input('region_id');
        $cities = City::where('region_id', $regionId)->select('id', 'city_name')->get();

        return response()->json([
            'message' => 'User profile updated successfully',
            'user' => $user,
            'cities' => $cities,
        ]);
    }



    /**
     * API endpoint to get regions and their associated cities.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function getcategoryApi()
    {
        $Category = StoreCategory::select('id', 'category_name_en', 'category_name_ar')->get();

        return response()->json(['Category' => $Category]);
    }
    public function getRegionsAndCitiesApi()
    {
        // Fetch all regions with their associated cities
        $regions = Region::with('cities:id,city_ar,city_en,region_id')->select('id', 'region_ar', 'region_en')->get();

        return response()->json(['regions' => $regions]);
    }

    public function updateAdminProfile(Request $request, user $user)

    {
        $lang = $request->input('lang');

        $userId = $request->input('userid');

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



    public function getUserInfoApi(Request $request)
    {

        // Fetch the user information based on the provided user ID
        $user = Auth::user();;

        // You can customize the data you want to include in the response

        return response()->json(['user' => $user]);
    }




    public function updateProfileWithOtp(Request $request)
    {

        $lang = $request->input('lang', 'en'); // Default to English if not provided

        // Validate the incoming request data
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date',
            'region' => 'required|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'photo' => 'image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file type and size as needed
            'otp' => 'required_if:mobile,' . ',!=' . Auth::user()->mobile, // OTP is required only if mobile number is different from the current user's mobile
        ]);

        // Check if the provided mobile number is different from the current one


        // Continue with the existing update logic for other fields

        // Update the user's profile information
        $userId = Auth::user()->id;
        $user = User::find($userId);
        $user->first_name = $request->input('first_name');
        $user->last_name = $request->input('last_name');
        $user->birthday = $request->input('birthday');
        $user->region = $request->input('region');
        $user->email = $request->input('email');
        $user->photo = $request->input('photo');

        // Check if a new profile image was uploaded
        // Check if a new profile image was uploaded
        if ($request->hasFile('photo')) {
            // Delete the old profile image (if it exists)
            if ($user->photo) {
                $oldImagePath = public_path('FrontEnd/assets/images/user_images/' . $user->photo);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Store the new profile image
            $image = $request->file('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('FrontEnd/assets/images/user_images'), $imageName);
            $user->photo = $imageName;
        } elseif ($request->input('photo') === null) {
            // If input photo is null, retain the old photo
            $user->photo = Auth::user()->photo;
        }








        if ($request->input('mobile') !== Auth::user()->mobile) {

            $existingUserWithMobile = User::where('mobile', $request->input('mobile'))->first();

            if ($existingUserWithMobile) {
                // Mobile number is already in use, return an error response
                $errorMessage = $lang === 'ar' ? 'تم استخدام رقم الجوال المقدم بالفعل من قبل مستخدم آخر. يرجى اختيار رقم جوال مختلف.' : 'The provided mobile number is already in use by another user. Please choose a different mobile number.';
                return response()->json(['error' => $errorMessage], 422);
            }

            // Generate and send OTP

            // $otp = rand(100000, 999999); // Generate a 6-digit OTP (you can use a more secure method)

            $otp = "12345"; // Generate a 6-digit OTP (you can use a more secure method)


            // For simplicity, you can store the OTP in the session
            Session::put('otp', $otp);

            // Send the OTP to the user (you need to implement SMS or email sending here)


            $enteredOtp = $request->input('otp');

            $storedOtp = Session::get('otp');
            if (empty($enteredOtp) || is_null($enteredOtp)) {
                // Invalid or missing OTP, return an error response
                $errorMessage = $lang === 'ar' ? ' تم إرسال رمز إلى رقم واتس اب' . $request->input('mobile') . '. الرجاء إدخال الرمز.' : 'We have sent OTP code to whatsapp number ' . $request->input('mobile') . '. Please enter the code.';
                return response()->json(['error' => $errorMessage, "OTP" => true, "Success" => true], 200);
            }
            // Verify the entered OTP with the stored one
            if ($enteredOtp != $storedOtp) {
                // Invalid OTP, return an error response
                $errorMessage = $lang === 'ar' ? 'رمز OTP غير صالح. يرجى المحاولة مرة أخرى.' : 'Invalid OTP. Please try again.';
                return response()->json(['error' => $errorMessage], 422);
            }
            if ($enteredOtp = $storedOtp) {
                $user->mobile = $request->input('mobile');

                // return response()->json(['error' => 'Invalid OTP. Please try again.'], 422);
            }
        }

        // Continue with the existing update logic for other fields

        // Verify OTP

        // Save the updated user data
        $user->save();

        // Clear the stored OTP from the session
        Session::forget('otp');

        // Return a success response
        $successMessage = $lang === 'ar' ? 'تم تحديث الملف الشخصي بنجاح.' : 'Profile updated successfully.';
        return response()->json(['message' => $successMessage]);
    }


    public function changePassword(Request $request)
    {
        // Decode JSON request body
        $requestData = json_decode($request->getContent(), true);

        // Check if JSON decoding failed
        if ($requestData === null) {
            return response()->json(['error' => 'Invalid JSON payload.'], 400);
        }

        // Extract data from JSON
        $lang = isset($requestData['lang']) ? $requestData['lang'] : 'en';
        $oldPassword = isset($requestData['old_password']) ? $requestData['old_password'] : null;
        $newPassword = isset($requestData['new_password']) ? $requestData['new_password'] : null;
        $newPasswordConfirmation = isset($requestData['new_password_confirmation']) ? $requestData['new_password_confirmation'] : null;

        // Validation messages translation
        $messages = [
            'new_password.different' => $lang === 'ar' ? 'يجب أن تكون كلمة المرور الجديدة مختلفة عن كلمة المرور القديمة.' : 'The new password must be different from the old password.',
            'new_password.confirmed' => $lang === 'ar' ? 'تأكيد كلمة المرور الجديدة غير متطابق.' : 'The new password confirmation does not match.',
        ];

        // Validate the incoming request data
        $validator = Validator::make($requestData, [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|different:old_password|confirmed',
            'new_password_confirmation' => 'required|string|min:8',
        ], $messages);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        // Check if the new password matches the confirmation
        if ($newPassword !== $newPasswordConfirmation) {
            // Password confirmation doesn't match, return an error response
            $errorMessage = $lang === 'ar' ? 'تأكيد كلمة المرور لا يتطابق.' : 'Password confirmation does not match.';
            return response()->json(['error' => $errorMessage], 422);
        }

        // OTP verification successful, check the old password
        $userId = Auth::user()->id;
        $user = User::find($userId);
        if (!Hash::check($oldPassword, $user->password)) {
            // Old password doesn't match, return an error response
            $errorMessage = $lang === 'ar' ? 'كلمة المرور القديمة غير صحيحة.' : 'Old password is incorrect.';
            return response()->json(['error' => $errorMessage], 422);
        }

        // Old password matches, update the user's password
        $user->password = Hash::make($newPassword);
        $user->save();

        // Return a success response
        $successMessage = $lang === 'ar' ? 'تم تحديث كلمة المرور بنجاح.' : 'Password updated successfully.';
        return response()->json(['message' => $successMessage], 200);
    }

    public function resetPassword(Request $request)
    {

        $requestData = json_decode($request->getContent(), true);

        // Check if JSON decoding failed
        if ($requestData === null) {
            return response()->json(['error' => 'Invalid JSON payload.'], 400);
        }

        // Extract data from JSON
        $lang = isset($requestData['lang']) ? $requestData['lang'] : 'en';
        $emailOrMobile = isset($requestData['email_or_mobile']) ? $requestData['email_or_mobile'] : null;
        $otp = isset($requestData['otp']) ? $requestData['otp'] : null;
        $newPassword = isset($requestData['new_password']) ? $requestData['new_password'] : null;
        $newPasswordConfirmation = isset($requestData['new_password_confirmation']) ? $requestData['new_password_confirmation'] : null;

        // Validation messages translation
        $messages = [
            'new_password.different' => $lang === 'ar' ? 'يجب أن تكون كلمة المرور الجديدة مختلفة عن كلمة المرور القديمة.' : 'The new password must be different from the old password.',
            'new_password.confirmed' => $lang === 'ar' ? 'تأكيد كلمة المرور الجديدة غير متطابق.' : 'The new password confirmation does not match.',
            'new_password.min' => $lang === 'ar' ? 'يجب أن تتكون كلمة المرور الجديدة من الأقل 8 أحرف.' : 'The new password must be at least 8 characters.',

        ];


        // Step 1: Check if the user exists
        $validator = Validator::make($requestData, [
            'email_or_mobile' => 'required|string',
            'new_password' => 'nullable|min:8|confirmed',
            'new_password_confirmation' => 'nullable|min:8',
        ], $messages);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }


        $user = User::where('email', $emailOrMobile)
            ->orWhere('mobile', $emailOrMobile)
            ->first();

        if (!$user) {
            // User not found, return an error response
            $errorMessage = $lang === 'ar' ? 'لم يتم العثور على مستخدم مرتبط بالبريد الإلكتروني أو الجوال المقدم.' : 'No user associated with the provided email or mobile.';
            return response()->json(['error' => $errorMessage], 404);
        }
        $mobilenumber =  '(+966)' . $user->mobile;
        // $mobilenumberRecive =  '966' . $user->mobile;
        $mobilenumberRecive =  '20' .'1150529992';
        $mobilenumberAR =  $user->mobile . '(966+)';

        // Step 2: Verify the OTP
        // $storedOtp = "12345";
        $storedOtp = rand(10000, 99999); 
        $userLanguage = $user->lang;
        
        // Set $lang based on the user's language
        $lang = ($userLanguage === 'ar') ? 'en_US' : 'en_US';

        $recipientNumber = $mobilenumberRecive;

        // Message content to be sent
        $messageContent = $storedOtp;

        // Call the sendWhatsAppMessage function from the AuthController
        $code = AuthController::sendWhatsAppMessage($lang,$recipientNumber, $messageContent);
        // $code;

        if (empty($otp) || is_null($otp)) {
            // Invalid or missing OTP, return an error response
           
            $errorMessage = $lang === 'ar' ? "تم ارسال رمز التفعيل عبر الواتس اب الي رقم $mobilenumberAR من فضلك ادخل كود التفعيل " : "We have sent OTP code to whatsapp number $mobilenumber. Please enter the code.";
            return response()->json(['success' => true, 'step' => 2, 'message' => $errorMessage,], 200);
        }

        if ($otp != $storedOtp) {
            // Incorrect OTP, return an error response
            $errorMessage = $lang === 'ar' ? ' كود  otp  غير صحيح' : 'incorrect OTP code, please check and try again ';
            return response()->json(['error' => $errorMessage, "OTP" => true, "Success" => true], 200);
        }
        

        // Step 3: Check if new password is empty
        if (empty($newPassword)) {

            // New password is empty, return an error response
            $errorMessage = $lang === 'ar' ? '.ادخل كلمة سر جديدة' : 'enter new password.';
            return response()->json(['message' => $errorMessage, 'step' => 3], 200);
        }
        if ($newPassword !== $newPasswordConfirmation) {
            // Password confirmation doesn't match, return an error response
            $errorMessage = $lang === 'ar' ? 'تأكيد كلمة السر لا يتطابق.' : 'Password confirmation does not match.';
            return response()->json(['error' => $errorMessage], 422);
        }
        // Step 4: Set the new password
        $user->password = Hash::make($newPassword);
        $user->save();

        // Clear the stored OTP from the session
        Session::forget('reset_password_otp');

        // Return a success response
        $successMessage = $lang === 'ar' ? 'تم تحديث كلمة السر  بنجاح.' : 'Password updated successfully.';
        return response()->json(['message' => $successMessage, "Success" => true, "reseted" => true], 200);
    }

    public function updateDeviceInfo(Request $request)
    {
        // Decode JSON request body
        $requestData = json_decode($request->getContent(), true);

        // Check if JSON decoding failed
        if ($requestData === null) {
            return response()->json(['error' => 'Invalid JSON payload.'], 400);
        }

        // Validate the incoming request data
        $validator = Validator::make($requestData, [
            'device_token' => 'nullable|string',
            'platform' => 'nullable|string',
            'platform_version' => 'nullable|string',
            'platform_device' => 'nullable|string',
            'lang' => 'nullable|string', // Add validation for 'lang'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        $userId = Auth::user()->id;
        $user = User::find($userId);

        // Update the user's device token, platform, platform version, and lang
        $user->device_token = $requestData['device_token'] ?? $user->device_token;
        $user->platform = $requestData['platform'] ?? $user->platform;
        $user->platform_version = $requestData['platform_version'] ?? $user->platform_version;
        $user->platform_device = $requestData['platform_device'] ?? $user->platform_device;
        $user->lang = $requestData['lang'] ?? $user->lang; // Update lang

        // Save the updated user data
        $user->save();

        // Return a success response
        return response()->json(['message' => 'Device information updated successfully']);
    }

    public function getAllUsers()
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Fetch all users
        $users = User::all();

        // Return the list of users
        return response()->json(['users' => $users]);
    }



    // Method to update a user's profile
    public function updateUser(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'birthday' => 'required|date',
            'region' => 'required|string|max:255',
            'mobile' => 'required|string|max:20',
            'email' => 'required|string|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Adjust file type and size as needed
            'lang' => 'nullable|string|in:en,ar', // Allow only 'en' and 'ar' for language
        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // (!Auth::user()->is_admin)ser is an admin
        if (!Auth::user()->is_admin) {
            return response()->json(['error' => trans('auth.unauthorized')], 403);
        }

        // Determine response language
        $lang = $request->input('lang', 'en');
        app()->setLocale($lang);

        // Retrieve the user by ID
        $user = User::find($request->input('user_id'));

        // Check if the user exists
        if (!$user) {
            $errorMessage = $lang === 'ar' ? 'لم يتم العثور على المستخدم.' : 'User not found.';
            return response()->json(['error' => $errorMessage], 404);
        }

        // Update the user's profile
        $userData = $request->only([
            'first_name',
            'last_name',
            'gender',
            'birthday',
            'region',
            'mobile',
            'email',
        ]);




        if ($request->hasFile('photo')) {
            // Delete the old profile image (if it exists)
            if ($user->photo) {
                $oldImagePath = public_path('FrontEnd/assets/images/user_images/' . $user->photo);
                if (File::exists($oldImagePath)) {
                    File::delete($oldImagePath);
                }
            }

            // Store the new profile image
            $image = $request->file('photo');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('FrontEnd/assets/images/user_images'), $imageName);
            $user->photo = $imageName;
        }


        // Apply updates to the user's profile
        $user->update($userData);

        // Return a success response
        $message = $lang === 'ar' ? 'تم تحديث الملف الخاص بالمستخدم بنجاح' : 'User profile updated successfully';
        return response()->json(['message' => $message]);
    }


    public function getUsersStatistics()
    {

        if (!Auth::user()->is_admin) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Count of all users
        $totalUsersCount = User::count();

        // Count of users who are vendors
        $vendorsCount = User::where('is_vendor', true)->count();

        // Count of users who are not vendors
        $nonVendorsCount = $totalUsersCount - $vendorsCount;

        // Count of discounts where discounts_status is "working"
        $workingDiscountsCount = Discount::where('discounts_status', 'working')->count();

        // Count of user discounts
        $userDiscountsCount = UserDiscount::count();

        // Sum of total payments for stores
        $totalPaymentsSum = UserDiscount::sum('after_discount');

        // Fetch the website manager
        $websiteManager = WebsiteManager::first();

        // Calculate profits
        $profits = UserDiscount::where('obtained_status', 0)->sum('obtained');

        // Calculate the percentage of vendors and non-vendors
        $vendorsPercentage = $totalUsersCount > 0 ? ($vendorsCount / $totalUsersCount) * 100 : 0;
        $nonVendorsPercentage = $totalUsersCount > 0 ? ($nonVendorsCount / $totalUsersCount) * 100 : 0;

        // Format the percentages
        $vendorsPercentage = number_format($vendorsPercentage, 2);
        $nonVendorsPercentage = number_format($nonVendorsPercentage, 2);

        // Retrieve all users, stores, and requests
        $users = User::all();
        $stores = Store::all();
        $regions = Region::all();
        $storeCategories = StoreCategory::all();
        $WebsiteManager = WebsiteManager::all();
        // Fetch all requests with approved status as 0
        $requests = Requests::where('approved', 0)->get();

        // Initialize an empty array to store formatted data
        $formattedRequests = [];
        $typeNames = [
            'update_store' => [
                'en' => 'Update Store',
                'ar' => 'تحديث المتجر',
            ],
            'delete_discount' => [
                'en' => 'Delete Discount',
                'ar' => 'حذف الخصم',
            ],
            'create_store' => [
                'en' => 'Create Store',
                'ar' => 'إنشاء متجر',
            ],
            'delete_store' => [
                'en' => 'Delete Store',
                'ar' => 'حذف المتجر',
            ],
        ];

        $attributeTranslations = [
            'name' => ['en' => 'Name', 'ar' => 'الاسم'],
            'location' => ['en' => 'Location', 'ar' => 'الموقع'],
            'phone' => ['en' => 'Phone', 'ar' => 'الهاتف'],
            'photo' => ['en' => 'Photo', 'ar' => 'الصورة'],
            'tax_number' => ['en' => 'Commercial Registration No', 'ar' => 'السجل التجاري'],
            'work_days' => ['en' => 'Work Days', 'ar' => 'أيام العمل'],
            'region' => ['en' => 'Region', 'ar' => 'المنطقة'],
            'category_id' => ['en' => 'category', 'ar' => 'الفئة'],
            'latitude' => ['en' => 'Latitude', 'ar' => 'خط العرض'],
            'longitude' => ['en' => 'Longitude', 'ar' => 'خط الطول'],
        ];
        // Loop through each request
        foreach ($requests as $request) {
            // Get user and store names using relationships
            $userName = $request->user->first_name . ' ' . $request->user->last_name;
            $storeName = $request->store->name;


            // Get type name based on the type value and current language
            $typeNameEn = isset($typeNames[$request->type]['en']) ? $typeNames[$request->type]['en'] : $request->type;
            $typeNameAr = isset($typeNames[$request->type]['ar']) ? $typeNames[$request->type]['ar'] : $request->type;

            if ($request->type == 'update_store') {
                // Get the new store data from the JSON "data" column
                $newStoreData = json_decode($request->data, true);

                // Get the old store data from the database
                $oldStore = Store::find($request->store_id);
                $oldStoreData = $oldStore->toArray();

                // Compare the attributes and identify differences
                $differences = [];
                foreach ($newStoreData as $key => $value) {
                    // Check if the attribute exists in the old data
                    if (array_key_exists($key, $oldStoreData)) {
                        // Compare the values only if they are different
                        if ($value != $oldStoreData[$key]) {
                            // Add the difference to the list
                            $attributeTranslationEn = $attributeTranslations[$key]['en'] ?? $key;
                            $attributeTranslationAr = $attributeTranslations[$key]['ar'] ?? $key;

                            if ($key === 'work_days') {
                                // Decode the old and new work days from JSON
                                $oldWorkDays = json_decode($oldStoreData[$key], true);
                                $newWorkDays = json_decode($value, true);

                                // Compare each day's work hours
                                foreach ($newWorkDays as $day => $hours) {
                                    if (!isset($oldWorkDays[$day]) || $oldWorkDays[$day] !== $hours) {
                                        // Add the difference to the list
                                        $differences[] = [
                                            'attribute_name_en' => $attributeTranslationEn . ' (' . $day . ')',
                                            'attribute_name_ar' => $attributeTranslationAr . ' (' . $day . ')',
                                            'attribute' => $key,
                                            'old_value_en' => isset($oldWorkDays[$day]) ? (int)$oldWorkDays[$day] : null,
                                            'old_value_ar' => isset($oldWorkDays[$day]) ? (int)$oldWorkDays[$day] : null,
                                            'new_value_en' => (int)$hours,
                                            'new_value_ar' => (int)$hours,
                                        ];
                                    }
                                }
                            } elseif ($key === 'region') {
                                // Retrieve the region name from the Region model
                                $oldRegion = Region::find($oldStoreData[$key]);
                                $oldRegionNameEn = $oldRegion ? $oldRegion->region_en : null;
                                $oldRegionNameAr = $oldRegion ? $oldRegion->region_ar : null;

                                $newRegion = Region::find($value);
                                $newRegionNameEn = $newRegion ? $newRegion->region_en : null;
                                $newRegionNameAr = $newRegion ? $newRegion->region_ar : null;

                                // Add the difference to the list
                                $differences[] = [
                                    'attribute_name_en' => $attributeTranslationEn,
                                    'attribute_name_ar' => $attributeTranslationAr,
                                    'attribute' => $key,
                                    'old_value' => $oldStoreData[$key],
                                    'new_value' => $value,
                                    'old_value_en' => $oldRegionNameEn !== null ? $oldRegionNameEn : null,
                                    'old_value_ar' => $oldRegionNameAr !== null ? $oldRegionNameAr : null,
                                    'new_value_en' => $newRegionNameEn !== null ? $newRegionNameEn : null,
                                    'new_value_ar' => $newRegionNameAr !== null ? $newRegionNameAr : null,
                                ];
                            } elseif ($key === 'category_id') {
                                // Retrieve the category name from the Category model
                                $oldCategory = StoreCategory::find($oldStoreData[$key]);
                                $oldCategoryNameEn = $oldCategory ? $oldCategory->category_name_en : null;
                                $oldCategoryNameAr = $oldCategory ? $oldCategory->category_name_ar : null;

                                $newCategory = StoreCategory::find($value);
                                $newCategoryNameEn = $newCategory ? $newCategory->category_name_en : null;
                                $newCategoryNameAr = $newCategory ? $newCategory->category_name_ar : null;

                                // Add the difference to the list
                                $differences[] = [
                                    'attribute_name_en' => $attributeTranslationEn,
                                    'attribute_name_ar' => $attributeTranslationAr,
                                    'attribute' => $key,
                                    'old_value' => $oldStoreData[$key],
                                    'new_value' => $value,
                                    'old_value_en' => $oldCategoryNameEn !== null ? $oldCategoryNameEn : null,
                                    'old_value_ar' => $oldCategoryNameAr !== null ? $oldCategoryNameAr : null,
                                    'new_value_en' => $newCategoryNameEn !== null ? $newCategoryNameEn : null,
                                    'new_value_ar' => $newCategoryNameAr !== null ? $newCategoryNameAr : null,
                                ];
                            } else {
                                // For attributes other than "work_days" and "region", directly compare the values
                                $differences[] = [
                                    'attribute_name_en' => $attributeTranslationEn,
                                    'attribute_name_ar' => $attributeTranslationAr,
                                    'attribute' => $key,
                                    'old_value_en' => $oldStoreData[$key] !== null ? (int)$oldStoreData[$key] : null,
                                    'old_value_ar' => $oldStoreData[$key] !== null ? (int)$oldStoreData[$key] : null,
                                    'new_value_en' => (int)$value,
                                    'new_value_ar' => (int)$value,
                                ];
                            }
                        }
                    }
                }



                // Add differences to the formatted array
                $formattedRequests[] = [
                    'id' => $request->id,
                    'user_id' => $request->user_id,
                    'user_name' => $userName,
                    'store_id' => $request->store_id,
                    'store_name' => $storeName,
                    'type' => $request->type,
                    'type_name_en' => $typeNameEn,
                    'type_name_ar' => $typeNameAr,
                    'differences' => $differences,
                ];
            } else {
                // If the request type is not "update_store," add basic information without comparisons
                $formattedRequests[] = [
                    'id' => $request->id,
                    'user_id' => $request->user_id,
                    'user_name' => $userName,
                    'store_id' => $request->store_id,
                    'store_name' => $storeName,
                    'type' => $request->type,
                    'type_name_en' => $typeNameEn,
                    'type_name_ar' => $typeNameAr,
                ];
            }
        }
        $userDiscounts = UserDiscount::orderBy('id', 'desc')->get();





        // Retrieve all stores
        $stores = Store::all();

        // Array to store stores with unobtained discounts
        $storesWithUnobtainedDiscounts = [];

        // Loop through each store
        foreach ($stores as $store) {
            // Check if the store has any UserDiscounts with obtained_status = 0
            $unobtainedDiscountsCount = UserDiscount::where('store_id', $store->id)
                ->where('obtained_status', 0)
                ->count();
            // Calculate the sum of obtained discounts
            $obtainedDiscountsSum = UserDiscount::where('store_id', $store->id)
                ->where('obtained_status', 0)
                ->sum('obtained');

            $unobtainedDiscounts = UserDiscount::where('store_id', $store->id)
                ->where('obtained_status', 0)
                ->select('id', 'store_id', 'user_id', 'discount_id', 'total_payment', 'after_discount', 'date', 'obtained_status', 'obtained')
                ->get();

            // If there are unobtained discounts, add the store to the result array
            if ($unobtainedDiscountsCount > 0) {
                $storesWithUnobtainedDiscounts[] = [
                    'store_name' => $store->name,
                    'store_id' => $store->id,
                    'unobtained_discounts_count' => $unobtainedDiscountsCount,
                    'obtained_discounts_sum' => $obtainedDiscountsSum,
                    'unobtained_discounts' => $unobtainedDiscounts

                ];
            }
        }

        // Prepare the response
        $statistics = [
            'total_users' => $totalUsersCount,
            'vendors_count' => $vendorsCount,
            'non_vendors_count' => $nonVendorsCount,
            'vendors_percentage' => $vendorsPercentage . '%',
            'non_vendors_percentage' => $nonVendorsPercentage . '%',
            'working_discounts_count' => $workingDiscountsCount,
            'user_discounts_count' => $userDiscountsCount,
            'total_payments_sum' => $totalPaymentsSum,
            'profits' => $profits,
        ];

        return response()->json([
            'statistics' => $statistics,
            'users' => $users, 
            'stores' => $stores, 
            'requests' => $formattedRequests, 
            'user_discounts' => $userDiscounts, 
            'websiteManager' => $websiteManager, 
            'storesDiscounts' => $storesWithUnobtainedDiscounts,
            'regions'=>$regions,
            'storeCategories'=>$storeCategories,
            'WebsiteManager'=> $WebsiteManager
        ]);
    }


    // $websiteManager = WebsiteManager::first();
    // 

    public function updateUserStatus(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'query' => 'required|string',
        ]);

        // Check for validation errors
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Retrieve the user by ID
        $user = User::find($request->input('user_id'));

        // Check if the user exists
        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Retrieve the query string
        $query = $request->input('query');

        // Update user status based on the query
        switch ($query) {
            case 'ban':
                $user->is_banned = 1;
                $message = 'User banned successfully.';
                break;
            case 'unban':
                $user->is_banned = 0;
                $message = 'User unbanned successfully.';
                break;
            case 'temporarily':
                $user->is_temporarily = 1;
                $message = 'User marked as temporarily suspended successfully.';
                break;
            case 'restore_temporary':
                $user->is_temporarily = 0;
                $message = 'User restored from temporary suspension successfully.';
                break;
            case 'make_vendor':
                $user->is_vendor = 1;
                $message = 'User set as vendor successfully.';
                break;
            case 'remove_vendor':
                $user->is_vendor = 0;
                $message = 'Vendor status removed from user successfully.';
                break;
            case 'make_admin':
                $user->is_admin = 1;
                $message = 'User set as admin successfully.';
                break;
            case 'remove_admin':
                $user->is_admin = 0;
                $message = 'Admin status removed from user successfully.';
                break;
            case 'delete':
                $user->is_deleted = 1;
                $message = 'Admin status removed from user successfully.';
                break;
            default:
                return response()->json(['error' => 'Invalid query.'], 422);
        }

        // Save the updated user status
        $user->save();

        // Return a success response
        return response()->json(['message' => $message]);
    }
}
