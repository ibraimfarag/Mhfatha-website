<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use  App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{
    public function dashboard_user(Request $request)
    {
        $user = Auth::user();

        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }
     
        return view('FrontEnd.profile.user-dashboard', compact('user'));
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


}
