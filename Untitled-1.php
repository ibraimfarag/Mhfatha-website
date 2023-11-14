//database\migrations\2014_10_12_000000_create_users_table.php


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('gender');
            $table->date('birthday');
            $table->string('city');
            $table->string('region');
            $table->string('mobile');
            $table->string('photo')->nullable();
            $table->string('email')->unique();
            $table->boolean('is_vendor')->default(0);
            $table->boolean('is_admin')->default(0);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}



//database\migrations\2023_10_27_145016_create_stores_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name');
            $table->string('location');
            $table->string('phone');
            $table->string('url_map')->nullable();
            $table->string('photo')->nullable();
            $table->string('qr')->nullable();
            $table->decimal('total_payments', 10, 2)->default(0);
            $table->decimal('total_withdrawals', 10, 2)->default(0);
            $table->unsignedInteger('count_times')->default(0);
            $table->string('work_hours');
            $table->string('work_days');
            $table->boolean('status')->default(1); // 1 for active, 0 for inactive
            $table->boolean('is_deleted')->default(0); // 1 for active, 0 for inactive
            $table->boolean('verifcation')->default(0); // 1 for active, 0 for inactive
            $table->boolean('is_bann')->default(0); // 1 for active, 0 for inactive
            $table->text('bann_msg')->nullable();
            $table->timestamps();
    
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stores');
    }
}


//database\migrations\2023_10_27_145131_create_discounts_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->decimal('percent', 5, 2);
            $table->string('category');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('discounts_status')->default('working'); // Default to 'working'
            $table->boolean('is_deleted')->default(0); // 1 for active, 0 for inactive

            $table->timestamps();
    
            $table->foreign('store_id')->references('id')->on('stores');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discounts');
    }
}


//database\migrations\2023_10_27_145217_create_user_discounts_table.php

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserDiscountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('store_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('discount_id');
            $table->decimal('total_payment', 10, 2);
            $table->decimal('after_discount', 10, 2);
            $table->date('date');
            $table->unsignedTinyInteger('status')->default(0);
            $table->text('reason')->nullable();
            $table->unsignedTinyInteger('obtained_status')->default(0);
            $table->decimal('obtained', 10, 2)->default(0.00);
            $table->text('notes')->nullable();
            $table->timestamps();
    
            $table->foreign('store_id')->references('id')->on('stores');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('discount_id')->references('id')->on('discounts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_discounts');
    }
}




/* -------------------------------------------------------------------------- */
/* --------------------------------- Models --------------------------------- */
/* -------------------------------------------------------------------------- */

//app\Models\Discount.php
<?php

namespace App\Models;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'store_id',
        'percent',
        'category',
        'start_date',
        'end_date',
        'discounts_status',
        "is_deleted",

    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value;

        // Automatically update discounts_status based on the end_date
        $currentDate = Carbon::now();
        $endDate = Carbon::parse($value);

        if ($endDate <= $currentDate) {
            $this->attributes['discounts_status'] = 'end';
        } else {
            $this->attributes['discounts_status'] = 'working';
        }
    }

}


//app\Models\Store.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'location',
        'phone',
        'url_map',
        'photo',
        'qr',
        'total_payments',
        'total_withdrawals',
        'count_times',
        'work_hours',
        'work_days',
        'status',
        'verifcation',
        'is_bann',
        'bann_msg',
        "is_deleted",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }
    public function userDiscounts()
    {
        return $this->hasMany(UserDiscount::class);
    }
}


//app\Models\User.php

<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable;

     protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'birthday',
        'city',
        'region',
        'mobile',
        'email',
        'photo',
        'is_vendor',
        'is_admin',
        'password',
    ];
    

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function stores()
    {
        return $this->hasMany(Store::class);
    }    
}


//app\Models\UserDiscount.php

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDiscount extends Model
{
    protected $fillable = [
        'store_id',
        'user_id',
        'discount_id',
        'total_payment',
        'after_discount',
        'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}




/* -------------------------------------------------------------------------- */
/* --------------------------------- routes --------------------------------- */
/* -------------------------------------------------------------------------- */

//routes\web.php

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\UserDiscountController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\DiscountController;

use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/






Route::get('/', [HomeController::class,'index'])->name('main');
Route::get('/home', [HomeController::class,'index'])->name('home');

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [AuthController::class, 'login_index'])->name('login');
    Route::post('/login-post', [AuthController::class, 'login_post'])->name('login_post');
    Route::get('/register', [AuthController::class, 'register_index'])->name('register');
    Route::post('/register-post', [AuthController::class, 'register_post'])->name('register_post');
});

Route::get('/switch-language', [LanguageController::class, 'switchLanguage'])->name('switchLanguage');



Route::middleware(['auth'])->group(function () {


    Route::get('/dashboard', [UserController::class, 'dashboard_user'])->name('dashboard_user');
    Route::get('/showProfile', [UserController::class, 'showProfile'])->name('profile');
    Route::put('/update-profile', [UserController::class, 'update_profile'])->name('profile-update');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::get('/password/change', [PasswordController::class,'showChangePasswordForm'])->name('password.change');
    Route::post('/password/change', [PasswordController::class,'changePassword'])->name('password.change');


    Route::get('/discounts/home', [UserDiscountController::class,'view'])->name('discount.view');






});

Route::middleware(['vendor'])->group(function () {

    Route::get('/stores', [StoreController::class,'index'])->name('Stores.view');

    Route::get('/stores/create', [StoreController::class,'create'])->name('stores.create');
    Route::post('/stores/store', [StoreController::class,'store'])->name('stores.store');


    Route::delete('stores/{store}', [StoreController::class,'destroy'])->name('stores.destroy');



    Route::get('/stores/edit', [StoreController::class, 'edit'])->name('stores.edit');

    Route::put('/stores/{store}', [StoreController::class, 'update'])->name('stores.update');
    
    Route::put('stores/{store}/verify', [StoreController::class, 'verify'])->name('stores.verify');
    
    Route::get('/discounts', [DiscountController::class, 'index'])->name('discounts.index');
    Route::get('/discounts/create/', [DiscountController::class, 'create'])->name('discounts.create');
Route::post('/discounts', [DiscountController::class, 'store'])->name('discounts.store');
Route::put('/discounts/{discount}', [DiscountController::class, 'update'])->name('discounts.update');
Route::delete('/discounts/{discount}', [DiscountController::class, 'destroy'])->name('discounts.destroy');


});

Route::middleware(['admin'])->group(function () {

});








/* -------------------------------------------------------------------------- */
/* ------------------------------- controllers ------------------------------ */
/* -------------------------------------------------------------------------- */



/* -------------------------------------------------------------------------- */
/* ----------------- app\Http\Controllers\AuthController.php ---------------- */
/* -------------------------------------------------------------------------- */

<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use  App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;


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

            return redirect()->intended('/dashboard' . '?lang=' . $currentLanguage);        }
    
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
    public function register_post (Request $request)
    {        $lang = $request->input('lang'); // Get the 'lang' parameter from the request

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
        ] ,$customMessages);

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
            // 'mobile' => $mobile, // Store the cleaned mobile number
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

}



/* -------------------------------------------------------------------------- */
/* --------------- app\Http\Controllers\DiscountController.php -------------- */
/* -------------------------------------------------------------------------- */


<?php
namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Discount;
use App\Models\Store;
use Illuminate\Support\Facades\App;
use DataTables;
class DiscountController extends Controller
{
    public function index(Store $store, Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        $storeid = $request->input('storeid');
        $store = Store::find($storeid);
        $discounts = $store->discounts->where('is_deleted', 0);

// dd($storeid);
        return view('FrontEnd.profile.discounts.index', compact('store', 'discounts'));
    }
    public function index_api(Store $store, Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        $storeid = $request->input('storeid');
        $store = Store::find($storeid);
        $discounts = $store->discounts->where('is_deleted', 0);

// dd($storeid);
return response()->json(['discounts' => $discounts]);
}

    public function create(Request $request)
    {
        $lang = $request->input('lang');
        // $storeiD =  $request->input('storeid');
        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }
        $storeid = $request->input('storeid');
        $store = Store::find($storeid);
// dd($storeid);
        return view('FrontEnd.profile.discounts.create',compact('store'));
    }

    public function store(Request $request)
    {
        

    $currentLanguage = $request->input('lang');


        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'percent' => 'required|numeric|min:0|max:100',
            'category' => 'required|string',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $discount = Discount::create([
            'store_id' => $request->store_id,
            'percent' => $request->percent,
            'category' => $request->category,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);



        return response()->json(['message' => 'Discount created successfully', 'discount' => $discount], 201);
    }

    public function edit(Discount $discount)
    {
        return view('discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $validator = Validator::make($request->all(), [
            'percent' => 'required|numeric',
            'category' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $discount->update($request->all());

        return redirect()->route('discounts.edit', $discount)->with('success', 'Discount updated successfully.');
    }

    public function destroy(Request $request, Discount $discount)
    {
        $discount->update(['is_deleted' => 1]);
        $lang = $request->input('lang');
        return back()->with('success', 'Discount deleted successfully')->with('lang', $lang);
    }
}

/* -------------------------------------------------------------------------- */
/* ----------------- app\Http\Controllers\HomeController.php ---------------- */
/* -------------------------------------------------------------------------- */


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        // Your controller logic for the home page

        return view('FrontEnd.home'); // Make sure to return the appropriate view
    }
}


/* -------------------------------------------------------------------------- */
/* --------------- app\Http\Controllers\LanguageController.php -------------- */
/* -------------------------------------------------------------------------- */

<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\App;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function switchLanguage(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        // Redirect back to the previous page or a specific route
        return redirect()->back();
    }
}

/* -------------------------------------------------------------------------- */
/* --------------- app\Http\Controllers\PasswordController.php -------------- */
/* -------------------------------------------------------------------------- */
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



/* -------------------------------------------------------------------------- */
/* ---------------- app\Http\Controllers\StoreController.php ---------------- */
/* -------------------------------------------------------------------------- */

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
/* -------------------------------------------------------------------------- */
/* ----------------- app\Http\Controllers\UserController.php ---------------- */
/* -------------------------------------------------------------------------- */

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

/* -------------------------------------------------------------------------- */
/* ------------- app\Http\Controllers\UserDiscountController.php ------------ */
/* -------------------------------------------------------------------------- */

<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\UserDiscount;
use Illuminate\Support\Facades\App;

use Illuminate\Http\Request;

class UserDiscountController extends Controller
{
    public function view(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }
     
        $userDiscounts = UserDiscount::with('store', 'discount')
        ->where('user_id', auth()->user()->id)
        ->get();
        return view('FrontEnd.profile.discounts', ['userDiscounts' => $userDiscounts]);
    }
    public function create(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }
     
        $userDiscounts = UserDiscount::where('user_id', auth()->user()->id)->get();

        return view('user_discounts.create', ['userDiscounts' => $userDiscounts]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Define validation rules for user discount creation
            'store_id' => 'required|exists:stores,id',
            'user_id' => 'required|exists:users,id',
            'discount_id' => 'required|exists:discounts,id',
            'total_payment' => 'required|numeric',
            'after_discount' => 'required|numeric',
            'date' => 'required|date',
            // Add more fields as needed
        ]);

        UserDiscount::create($data);

        return redirect()->route('user_discounts.create')->with('success', 'User discount created successfully.');
    }

    public function edit(UserDiscount $userDiscount)
    {
        return view('user_discounts.edit', compact('userDiscount'));
    }

    public function update(Request $request, UserDiscount $userDiscount)
    {
        $data = $request->validate([
            // Define validation rules for user discount updates
            'total_payment' => 'required|numeric',
            'after_discount' => 'required|numeric',
            'date' => 'required|date',
            // Add more fields as needed
        ]);

        $userDiscount->update($data);

        return redirect()->route('user_discounts.edit', $userDiscount)->with('success', 'User discount updated successfully.');
    }

    public function destroy(UserDiscount $userDiscount)
    {
        $userDiscount->delete();

        return redirect()->route('user_discounts.create')->with('success', 'User discount deleted successfully.');
    }
}




