<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use  App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login_index(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }
        return view('FrontEnd.Auth.login');
    }
    public function login_post(Request $request)
    {

        $currentLanguage = $request->input('lang');
        $credentials = $request->only('email_or_mobile', 'password');

        // Add a custom rule to identify whether the input is an email or a mobile number
        $field = filter_var($request->input('email_or_mobile'), FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        // Add the field name to the credentials array
        $credentials[$field] = $request->input('email_or_mobile');
        unset($credentials['email_or_mobile']);

        // Attempt to log in the user
        if (Auth::attempt($credentials)) {
            // Authentication passed
            Session::put('user_id', Auth::user()->id); // Create a session variable

            return redirect()->intended('/dashboard' . '?lang=' . $currentLanguage);
        }

        // Authentication failed, redirect back with an error message
        return redirect()
            ->back()
            ->withInput($request->only('email_or_mobile'))
            ->withErrors(['loginError' => 'Invalid credentials']);
    }
    public function register_index(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }
        return view('FrontEnd.Auth.register', ['lang' => $lang]); // Pass the 'lang' variable to the view
    }
    public function register_post(Request $request)
    {
        $lang = $request->input('lang'); // Get the 'lang' parameter from the request

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

        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'gender' => 'required|string', // Add gender field
            'birthday' => 'required|date',
            'city' => 'required|string|max:255',
            'region' => 'required|string|max:255',
            'mobile' => 'required|string|max:255|unique:users', // Ensure 'mobile' is unique
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8|confirmed', // Ensure password matches password_confirmation
        ], $customMessages);

        $mobileExists = User::where('mobile', $request->mobile)->exists();

        if ($mobileExists) {
            return redirect()->back()
                ->with('error', $customMessages)
                ->withInput();
        }
        // $mobile = str_replace('-', '', $request->mobile);

        // Create a new user record
        User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'gender' => $request->gender,
            'birthday' => $request->birthday,
            'city' => $request->city,
            'region' => $request->region,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'is_vendor' => $request->is_vendor,
            'password' => Hash::make($request->password),
            'photo' => 'default_user.png', // Set the default image path here

        ]);
        $successMessage = ($currentLanguage === 'ar') ? 'تم التسجيل بنجاح.' : ' Registration successful!';

        return redirect()->route('register', ['lang' => $lang])->with('success',  $successMessage);
    }
    public function logout()
    {
        Auth::logout(); // Log the user out
        Session::forget('user_id'); // Clear the user's session data
        return back(); // Redirect to the login page
    }





    // /* -------------------------------------------------------------------------- */
    // /* ----------------------------------- api ---------------------------------- */
    // /* -------------------------------------------------------------------------- */
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login_api(Request $request)
    {
        $credentials = $request->only('email_or_mobile', 'password');

        $field = filter_var($request->input('email_or_mobile'), FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $credentials[$field] = $request->input('email_or_mobile');
        unset($credentials['email_or_mobile']);

        if (Auth::attempt($credentials)) {
            $token = Str::random(60);

            /** @var \App\Models\User $user **/

            $user = Auth::user();
            $success['token'] =  $user->createToken('ApiToken')->accessToken;


            return response()->json([
          
                'token' => $success['token'],
                'success' => true,
                'message' => 'Login successful',
                'user' => Auth::user(),


            ], 200);
        }

        return response()->json(['error' => 'Invalid credentials'], 401);
    }


    public function register_api(Request $request)
    {
        try {
            $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            // Create a new user record
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json(['user' => $user, 'message' => 'Registration successful']);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }
    }
}
