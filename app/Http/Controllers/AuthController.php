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
        try {

        $requestData = $request->all();

        $currentLanguage = $requestData['lang'] ?? 'ar';
        if (!in_array($currentLanguage, ['ar', 'en'])) {
            $currentLanguage = 'ar';
        }

        // Validate the incoming request data
        $validator = Validator::make($requestData, [
            'first_name' => 'required',
            'last_name' => 'required',
            'gender' => 'required',
            'birthday' => 'required',
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
        $mobilenumberRecive = '966' . $requestData['mobile'];
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
            'gender' => $requestData['gender'],
            'birthday' => $requestData['birthday'],
            'region' => $requestData['region'],
            'mobile' => $requestData['mobile'],
            'email' => $requestData['email'],
            'is_vendor' => $requestData['is_vendor'],
            'password' => Hash::make($requestData['password']),
        ]);

        $successMessage = ($currentLanguage === 'ar') ? 'تم التسجيل بنجاح.' : 'Registration successful!';

        return response()->json(['success' => true, 'message' => $successMessage]);
    }catch (\Exception $e) {
        Log::channel('per-error')->error('Register API Error: ' . $e->getMessage());
        Log::channel('api')->error('Error in register_api', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()  // Optionally include stack trace
        ]);
        // Optionally send the exception details in the response in a safe manner
        return response()->json([
            'success' => false, 
            'message' => 'An unexpected error occurred. Please try again.'
        ], 500);
    }



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
    public static function sendWhatsAppMessage($lang, $recipientNumber, $messageContent)
    {
        $accessToken = 'EAANDSztKdFQBOyD2vkZAKM5VIdz6JGeaMsZAqRxD6WShrKghUri8we90AmrktDEJRNJnZBNldhBOLpHTszvC2bZBRM72AGqfEi4LOyWCQKX6SboASzz4Cx82SpvLIjZAeXx21RtOcNnymON5DsC2ZAyj2ZBSit9EufQKm6s4nU5ReOLZALbCVo4sVDtdETvecZBBt';
        $graphApiUrl = 'https://graph.facebook.com/v18.0/183130461559224/messages';

        $postData = [
            "messaging_product" => "whatsapp",
            "recipient_type" => "individual",
            "to" => $recipientNumber,
            "type" => "template",
            "template" => [
                "name" => "otppassword",
                "language" => [
                    "code" => $lang
                    // "code" => "en_US"
                ],
                "components" => [
                    [
                        "type" => "body",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $messageContent
                            ]
                        ]
                    ],
                    [
                        "type" => "button",
                        "sub_type" => "url",
                        "index" => "0",
                        "parameters" => [
                            [
                                "type" => "text",
                                "text" => $messageContent
                            ]
                        ]
                    ]


                ]
            ]
        ];

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken
        ])->post($graphApiUrl, $postData);

        return $response->json();
    }
}
