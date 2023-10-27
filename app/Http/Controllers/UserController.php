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
    ]);

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
    // Redirect back to the profile page or wherever you want
    return redirect()->route('profile', ['lang' => $lang])->with('success', 'Profile updated successfully');
}


}
