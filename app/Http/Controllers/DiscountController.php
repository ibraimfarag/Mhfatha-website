<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Discount;
use App\Models\Store;
use App\Models\User;
use App\Models\UserDiscount;
use Illuminate\Support\Facades\App;
use DataTables;
use App\Models\Request as StoreRequest;
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
    public function getBadgeCounts()
    {
        $userId = auth()->id();

        // Your logic to get real-time badge counts
        $salesOrderBadgeCount = UserDiscount::whereHas('store.user', function ($query) use ($userId) {
            $query->where('id', $userId);
        })
            ->where('status', 3)
            ->count();
        // Set a fixed value for messages badge count (5 in this case)
        $messagesBadgeCount = 5;

        return response()->json([
            'salesOrderBadgeCount' => $salesOrderBadgeCount,
            'messagesBadgeCount' => $messagesBadgeCount,
        ]);
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
        return view('FrontEnd.profile.discounts.create', compact('store'));
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


    public function getDiscountsByStoreId(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'lang' => 'nullable|in:en,ar',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        $lang = $request->input('lang', 'en'); // Default to English if language not specified or invalid
    
        if ($lang == 'ar') {
            app()->setLocale('ar');
        }
    
        $store_id = $request->input('store_id');
    
        $discounts = Discount::where('store_id', $store_id)
            ->where('discounts_status', 'working')
            ->where('is_deleted', 0)
            ->get();
    
        if ($discounts->isEmpty()) {
            $message = ($lang == 'ar') ? 'لا توجد خصومات' : 'No discounts ';
            return response()->json(['message' => $message], 250);
        }
    
        return response()->json(['discounts' => $discounts]);
    }
    
    public function createStoreDiscount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'percent' => 'required|numeric|min:0|max:100',
            'category' => 'required|string',
            'start_date' => 'required|date_format:Y-m-d',
            'end_date' => 'required|date_format:Y-m-d|after:start_date',
            'lang' => 'nullable|in:en,ar', // Adding language validation
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $lang = $request->input('lang', 'en'); // Default to English if language not specified or invalid

        if ($lang == 'ar') {
            app()->setLocale('ar');
        }

        $discount = Discount::create([
            'store_id' => $request->store_id,
            'percent' => $request->percent,
            'category' => $request->category,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'discounts_status' => 'working', // Assuming the default status is active
            'is_deleted' => 0, // Assuming the discount is not deleted upon creation
        ]);

        $message = ($lang == 'ar') ? 'تم إنشاء الخصم بنجاح' : 'Discount created successfully';

        return response()->json(['message' => $message, 'discount' => $discount], 201);
    }


    public function createDeleteDiscountRequest(Request $request)
    {
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'discount_id' => 'required|exists:discounts,id',
            'lang' => 'required|in:en,ar',
        ]);
    
        // Check if validation fails
        if ($validator->fails()) {
            $errors = $validator->errors();
            return response()->json(['status' => 'error', 'errors' => $errors], 422);
        }
    
        $lang = $request->input('lang');
    
        $discount = Discount::find($request->discount_id);
        $store = $discount->store_id;
    
        // Check if the discount exists
        if (!$discount) {
            return response()->json([
                'status' => 'error',
                'message' => ($lang === 'ar') ? 'الخصم غير موجود.' : 'Discount not found.',
            ], 404);
        }
    
        // Create a request for deleting the discount
        $requestData = [
            'user_id' => auth()->id(),
            'store_id' => $store,
            'type' => 'delete_discount',
            'data' => json_encode(['discount_id' => $request->discount_id]),
            'approved' => false, // Set to false initially as it needs approval
        ];
    
        // Add the request to the requests table
        $newRequest = StoreRequest::create($requestData);
    
        // Return a response indicating that a request has been sent for approval
        return response()->json([
            'status' => 'success',
            'message' => ($lang === 'ar') ? 'تم إرسال طلب حذف الخصم بنجاح.' : 'Delete discount request sent successfully.',
            'request_id' => $newRequest->id,
        ]);
    }
    }
