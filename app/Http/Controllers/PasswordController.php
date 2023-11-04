<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Str;
use Illuminate\Contracts\Auth\Authenticatable;
use App\Events\PasswordChanged;
use Illuminate\Support\Facades\App;



class PasswordController extends Controller
{
   // Change Password Form
   public function showChangePasswordForm(Request $request)
   {
    $lang = $request->input('lang');

    if ($lang && in_array($lang, ['en', 'ar'])) {
        App::setLocale($lang);
    }
       return view('FrontEnd.profile.ChangePassword');
   }

   // Change Password
   public function changePassword(Request $request)
   {
    $lang = $request->input('lang');
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



       // Get the authenticated user from the request
       $user = $request->user();
   
       $this->validate($request, [
           'current_password' => 'required',
           'password' => 'required|string|min:8|confirmed',
       ],$customMessages);
       $successMessage = ($currentLanguage === 'ar') ? 'تم تغير كلمة السر بنجاح.' : ' Password changed successfully';
       $incorrectMessage = ($currentLanguage === 'ar') ? 'كلمة السر الحالية غير صحيحة' : ' The current password is incorrect.';

       if (Hash::check($request->current_password, $user->password)) {
           $user->password = Hash::make($request->password);
           $user->save();
           
           event(new PasswordChanged($user));
   
           return back()->with('success',  $successMessage);
       } else {
           return back()->withErrors(['current_password' => $incorrectMessage]);
       }
   }

   // Reset Password Form
   public function showResetPasswordForm()
   {
       return view('auth.passwords.email');
   }

   // Send Password Reset Link
   public function sendResetLinkEmail(Request $request)
   {
       $this->validate($request, ['email' => 'required|email']);

       $response = Password::sendResetLink($request->only('email'));

       return $response == Password::RESET_LINK_SENT
           ? back()->with('status', __($response))
           : back()->withErrors(['email' => __($response)]);
   }

   // Reset Password Form
   public function showResetForm(Request $request, $token = null)
   {
       return view('auth.passwords.reset')->with(
           ['token' => $token, 'email' => $request->email]
       );
   }

   // Reset Password
   public function reset(Request $request)
   {
       $this->validate($request, [
           'token' => 'required',
           'email' => 'required|email',
           'password' => 'required|string|min:8|confirmed',
       ]);

       $response = Password::reset(
           $request->only('email', 'password', 'password_confirmation', 'token'),
           function ($user, $password) {
               $user->forceFill([
                   'password' => Hash::make($password),
                   'remember_token' => Str::random(60),
               ])->save();

               event(new PasswordReset($user));
           }
       );

       return $response == Password::PASSWORD_RESET
           ? redirect()->route('login')->with('status', __($response))
           : back()->withInput($request->only('email'))->withErrors(['email' => __($response)]);
   }

}
