<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use  App\Models\User;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Artisan;
use Twilio\Rest\Client;


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

    /**
     * API endpoint to get regions and their associated cities.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register_post(Request $request)
    {
        // Retrieve all input data as JSON
        $requestData = $request->json()->all();
        $lang = $requestData['lang'] ?? 'en'; // Default to 'en' if 'lang' is not provided
    
    
    
        return response()->json([
            'success' => true,
            'message' => $requestData
        ], 201);
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
        $currentLanguage = $request->input('lang');

        $credentials = $request->only('email_or_mobile', 'password');

        $field = filter_var($request->input('email_or_mobile'), FILTER_VALIDATE_EMAIL) ? 'email' : 'mobile';

        $credentials[$field] = $request->input('email_or_mobile');
        unset($credentials['email_or_mobile']);


        if (Auth::attempt($credentials)) {
            // $token = Str::random(60);

            /** @var \App\Models\User $user **/

            $user = Auth::user();
            // $success['token'] =  $user->createToken('ApiToken')->accessToken;
            $user->tokens()->delete();
            $token = $user->createToken('ApiToken')->accessToken;

            if ($user->is_banned) {
                $errorMessage = ($currentLanguage == 'ar') ? 'تم حظر حسابك، يرجى الاتصال بالدعم لمزيد من المساعدة' : 'Your account has been banned, please contact support for further assistance';
                return response()->json(['error' => $errorMessage], 403);
            }

            if ($user->is_deleted) {
                $errorMessage = ($currentLanguage == 'ar') ? 'تم حذف حسابك، يرجى الاتصال بالدعم لاسترداد حسابك' : 'Your account has been deleted, please contact support to recover your account';
                return response()->json(['error' => $errorMessage], 403);
            }

            return response()->json([

                // 'token' => $success['token'],
                'token' => $token,
                'success' => true,
                'message' => 'Login successful',
                'user' => Auth::user(),


            ], 200);


            if ($currentLanguage == 'ar') {
                $response['message'] = 'تم تسجيل الدخول بنجاح';
            } else {
                $response['message'] = 'Login successful';
            }

            return response()->json($response, 200);
        }

        $errorMessage = ($currentLanguage == 'ar') ? 'البريد الإلكتروني/الجوال أو كلمة المرور غير صحيحه' : 'Invalid email/mobile or password';

        return response()->json(['error' => $errorMessage], 401);
    }
    /**
     * register_api
     *
     * @return \Illuminate\Http\Response
     */
    public function register_api(Request $request)
    {
       
        $requestData = $request->all();

        $currentLanguage = $requestData['lang'] ?? 'ar';
        if (!in_array($currentLanguage, ['ar', 'en'])) {
            $currentLanguage = 'ar';
        }

        // Validate the incoming request data
        $validator = Validator::make($requestData, [
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'nullable',
            'birthday' => 'nullable',
            'region' => 'required',
            'mobile' => 'required|digits:10| unique:users',
            'email' => 'required|email|unique:users',
            'is_vendor' => 'required',
            'password' => 'required|min:8|confirmed',

        ]);
        $lang  = $requestData['lang'] ?? 'ar';
        if (!in_array($lang, ['ar', 'en'])) {
            $lang  = 'ar';
        }

        $langs = ($lang === 'ar') ? 'en_US' : 'en_US';
        $otp_static = '12345';
        // Check if validation fails
        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();

            // Translate error messages to Arabic if the current language is Arabic
            if ($currentLanguage === 'ar') {
                $translatedErrorMessages = [];

                foreach ($errorMessages as $errorMessage) {
                    // Translate each error message here (you may replace this with your actual translations)
                    $translatedErrorMessages[] = $this->translateErrorMessage($errorMessage);
                }
                
                return response()->json(['success' => false, 'messages' => $translatedErrorMessages], 400);
            }

            return response()->json(['success' => false, 'messages' => $errorMessages], 400);
        }

        // Check if photo_base64 field is provided and validate its format
        // (Code related to photo handling is commented out for brevity)

        $mobilenumber = '(+966)' . $requestData['mobile'];
        $mobilenumberAR = $requestData['mobile'] . '(966+)';
        $mobilenumberRecive = '+966' . $requestData['mobile'];
        $recipientNumber = $mobilenumberRecive;
        $otp = Cache::get('register' . $requestData['mobile']);

        if (!$otp) {
            // Generate a new OTP
            $otp = rand(10000, 99999);

            // Cache the OTP with a TTL of 5 minutes (300 seconds)
            Cache::put('register' . $requestData['mobile'], $otp, 300);
        }

        // Check if a new photo was uploaded
        // (Code related to photo handling is commented out for brevity)

        $messageContent = $otp;

        $testvar = trim(Cache::get('register' . $requestData['mobile']));
        $enteredOtp = isset($requestData['otp']) ? $requestData['otp'] : null;
        if (empty($enteredOtp) || is_null($enteredOtp)) {
            // OTP is required, return an error response
            $code = AuthController::sendWhatsAppMessage($langs, $recipientNumber, $messageContent);

            $errorMessage = $currentLanguage === 'ar' ? "تم ارسال رمز التفعيل عبر الواتس اب الي رقم $mobilenumberAR من فضلك ادخل كود التفعيل " : "We have sent OTP code to whatsapp number $mobilenumber. Please enter the code.";
            return response()->json(['success' => true, "OTP" => true, 'message' => $errorMessage, 'otp' => $testvar], 200);
        }

        // Generate and send OTP
        // Check if the entered OTP matches the generated OTP
        if ($enteredOtp !== $testvar  ) {
            // Invalid OTP, return an error response
            $errorMessage = $currentLanguage === 'ar' ? 'رمز OTP غير صالح. يرجى المحاولة مرة أخرى.' : 'Invalid OTP. Please try again.';
            return response()->json(['success' => false, 'message' => $errorMessage, 'otp' => $testvar], 400);
        }

        // Create a new user record
        User::create([
            'first_name' => $requestData['first_name'],
            'last_name' => $requestData['last_name'],
            'gender' => $requestData['gender'] ?? null,
            'birthday' => $requestData['birthday'] ?? null,
            'region' => $requestData['region'],
            'mobile' => $requestData['mobile'],
            'email' => $requestData['email'],
            'is_vendor' => $requestData['is_vendor'],
            'password' => Hash::make($requestData['password']),
        ]);

        $successMessage = ($currentLanguage === 'ar') ? 'تم التسجيل بنجاح.' : 'Registration successful!';

        return response()->json(['success' => true, 'message' => $successMessage]);
    



    }

    private function translateErrorMessage($errorMessage)
    {
        $translations = [
            'The first name field is required.' => 'يجب إدخال الاسم الأول.',
            'The last name field is required.' => 'يجب إدخال اسم العائلة.',
            'The gender field is required.' => 'يجب تحديد الجنس.',
            'The birthday field is required.' => 'يجب إدخال تاريخ الميلاد.',
            'The region field is required.' => 'يجب إدخال المنطقة.',
            'The mobile has already been taken.' => 'تم استخدام رقم الجوال بالفعل.',
            'The email has already been taken.' => 'تم استخدام البريد الإلكتروني بالفعل.',
            'The password must be at least 8 characters.' => 'يجب أن تكون كلمة المرور على الأقل 8 أحرف.',
            'The password confirmation does not match.' => 'تأكيد كلمة المرور غير متطابق.',
            'The mobile field is required.' => 'يجب إدخال رقم الجوال.',
            'The email field is required.' => 'يجب إدخال البريد الإلكتروني.',
            'The password field is required.' => 'يجب إدخال كلمة المرور.',
            // Add more translations as needed
        ];


        return $translations[$errorMessage] ?? $errorMessage;
    }


    public function checkInternetConnection()
    {

        return response()->json(['status' => 'connected']);
    }

    /**
     * Validate the provided token.
     *
     * @param string $token
     * @return bool
     */

    public function validateToken(Request $request)
    {
        $token = $request->bearerToken();
        $currentLanguage = $request->input('lang');
        $msg = '';

        if (!$token) {
            if ($currentLanguage == 'ar') {
                $msg = 'أنت مسجل الدخول من جهاز آخر';
            } else {
                $msg = 'You are logged in from another device';
            }
            return response()->json(['success' => false, 'message' => $msg], 200);
        }

        // Check if the token is valid
        if (Auth::guard('api')->check()) {
            return response()->json(['success' => true], 200);
        }

        if ($currentLanguage == 'ar') {
            $msg = 'أنت مسجل الدخول من جهاز آخر';
        } else {
            $msg = 'You are logged in from another device';
        }
        return response()->json(['success' => false, 'message' => $msg], 200);
    }
    /**
     * Send a WhatsApp message using the Facebook Graph API.
     *
     * @param string $recipientNumber The WhatsApp number of the recipient.
     * @param string $messageContent The content of the message.
     * @return string The response from the Facebook Graph API.
     */
    // public static function sendWhatsAppMessage($lang, $recipientNumber, $messageContent)
    // {
    //     $accessToken = 'EAANDSztKdFQBOyD2vkZAKM5VIdz6JGeaMsZAqRxD6WShrKghUri8we90AmrktDEJRNJnZBNldhBOLpHTszvC2bZBRM72AGqfEi4LOyWCQKX6SboASzz4Cx82SpvLIjZAeXx21RtOcNnymON5DsC2ZAyj2ZBSit9EufQKm6s4nU5ReOLZALbCVo4sVDtdETvecZBBt';
    //     $graphApiUrl = 'https://graph.facebook.com/v18.0/183130461559224/messages';

    //     $postData = [
    //         "messaging_product" => "whatsapp",
    //         "recipient_type" => "individual",
    //         "to" => $recipientNumber,
    //         "type" => "template",
    //         "template" => [
    //             "name" => "otppassword",
    //             "language" => [
    //                 "code" => $lang
    //                 // "code" => "en_US"
    //             ],
    //             "components" => [
    //                 [
    //                     "type" => "body",
    //                     "parameters" => [
    //                         [
    //                             "type" => "text",
    //                             "text" => $messageContent
    //                         ]
    //                     ]
    //                 ],
    //                 [
    //                     "type" => "button",
    //                     "sub_type" => "url",
    //                     "index" => "0",
    //                     "parameters" => [
    //                         [
    //                             "type" => "text",
    //                             "text" => $messageContent
    //                         ]
    //                     ]
    //                 ]


    //             ]
    //         ]
    //     ];

    //     $response = Http::withHeaders([
    //         'Content-Type' => 'application/json',
    //         'Authorization' => 'Bearer ' . $accessToken
    //     ])->post($graphApiUrl, $postData);

    //     return $response->json();
    // }






    public static function sendWhatsAppMessage($lang, $recipientNumber, $messageContent)
    {
        $response = self::sendSMS($recipientNumber, $messageContent);
        return $response;
    }
    
    private static function sendSMS($recipientNumber, $messageContent,$channel = 'sms')
    {
        // Your Twilio credentials
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $fromPhoneNumber = env('TWILIO_FROM');
        $twilio_verify_sid = env('TWILIO_VERIFY_SID');
        // Create a Twilio client
        $twilio = new Client($sid, $token);
    
        // Send an SMS message
        // $message = $twilio->messages
        //     ->create(
        //         $recipientNumber, // The recipient's phone number
        //         [
        //             "from" => $fromPhoneNumber,
        //             "body" => $messageContent
        //         ]
        //     );

    
        $verification = $twilio->verify->v2->services($twilio_verify_sid)
        ->verifications
        ->create($recipientNumber, $channel);

    return $verification->sid; // Return the verification SID for confirmation
    
}
    



    public function clearLogs()
    {
        // Define the log directory
        $logPath = storage_path('logs');

        // Get all log files
        $files = File::files($logPath);

        // Loop through each file and delete it
        foreach ($files as $file) {
            File::delete($file);
        }

        return response()->json(['status' => 'success', 'message' => 'Logs have been cleared.']);
    }








     /**
     * Clear all caches.
     *
     * @return \Illuminate\Http\Response
     */
    public function clearCache()
    {
        Artisan::call('cache:clear');
        Artisan::call('config:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');

        return response()->json(['success' => true, 'message' => 'All caches have been cleared.']);
    }


    public function registerPostJson(Request $request)
{
    // Retrieve all input data as JSON
    $requestData = $request->json()->all();
    $lang = $requestData['lang'] ?? 'en'; // Default to 'en' if 'lang' is not provided

    $currentLanguage = $lang;


    return response()->json([
        'success' => true,
        'message' => $requestData
    ], 201);
}

}
