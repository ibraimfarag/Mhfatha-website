<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\UserDiscount;
use App\Models\Discount;
use App\Models\User;
use App\Models\store;

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

    public function view_admin(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        $userDiscounts = UserDiscount::with('store', 'discount')
            ->where('user_id', auth()->user()->id)
            ->get();
        return view('FrontEnd.profile.discounts.admin', ['userDiscounts' => $userDiscounts]);
    }
    public function fetchDiscounts(Request $request)
    {
        $userFilter = $request->input('userFilter');
        $storeFilter = $request->input('storeFilter');
        $statusFilter = $request->input('statusFilter');
        $regionFilter = $request->input('regionFilter');
        $cityFilter = $request->input('cityFilter');

        $query = UserDiscount::select('user_discounts.*')
            ->join('users', 'users.id', '=', 'user_discounts.user_id')
            ->join('stores', 'stores.id', '=', 'user_discounts.store_id')
            ->join('discounts', 'discounts.id', '=', 'user_discounts.discount_id');

        if (!empty($userFilter)) {
            $query->where(function ($q) use ($userFilter) {
                $q->where('users.first_name', 'like', "%$userFilter%")
                    ->orWhere('users.last_name', 'like', "%$userFilter%");
            });
        }
        if (!empty($storeFilter)) {
            $query->where('stores.name', 'like', "%$storeFilter%");
        }
        if (!empty($statusFilter)) {
            $query->where('user_discounts.status', $statusFilter);
        }
        if (!empty($regionFilter)) {
            $query->where('users.region', 'like', "%$regionFilter%");
        }
        if (!empty($cityFilter)) {
            $query->where('stores.location', 'like', "%$cityFilter%");
        }

        $discounts = $query->get();

        $discountsData = $discounts->map(function ($discount) {
            return [
                'user_name' => $discount->user->first_name . ' ' . $discount->user->last_name,
                'user_region' => $discount->user->region,
                'user_gender' => $discount->user->gender,
                'store_name' => $discount->store->name,
                'store_city' => $discount->store->location,
                'category' => $discount->discount->category,
                'percent' => $discount->discount->percent,
                'after_discount' => $discount->after_discount,
                'status' => $discount->status,
                'reason' => $discount->reason,
            ];
        });

        return response()->json($discountsData);
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


    public function store_overview(Request $request)
    {
        $lang = $request->input('lang');

        if ($lang && in_array($lang, ['en', 'ar'])) {
            App::setLocale($lang);
        }

        $userDiscounts = UserDiscount::where('user_id', auth()->user()->id)->get();

        return view('FrontEnd.profile.discounts.storeOverview', ['userDiscounts' => $userDiscounts]);
    }


    public function postUserDiscount(Request $request)
    {
        try {
            // Validate the JSON input data
            $request->validate([
                'user_id' => 'required|integer',
                'store_id' => 'required|integer',
                'discount_id' => 'required|integer',
                'total_payment' => 'required|numeric',
                'lang' => 'nullable|string', // Add language validation
            ]);

            // Get JSON data from the request body
            $requestData = $request->json()->all();

            // Default language to English if not provided
            $lang = $requestData['lang'] ?? 'en';

            // Calculate the after_discount based on the discount percentage
            $discount = Discount::find($requestData['discount_id']);

            if (!$discount) {
                $message = $lang === 'ar' ? 'لم يتم العثور على الخصم' : 'Discount not found';
                return response()->json(['message' => $message], 404);
            }

            $percent = $discount->percent;
            $totalPayment = $requestData['total_payment'];

            $afterDiscount = $totalPayment - $totalPayment * ($percent / 100);

            // Create a new user discount entry
            $userDiscount = new UserDiscount();
            $userDiscount->user_id = $requestData['user_id'];
            $userDiscount->store_id = $requestData['store_id'];
            $userDiscount->discount_id = $requestData['discount_id'];
            $userDiscount->total_payment = $totalPayment;
            $userDiscount->after_discount = $afterDiscount;
            $userDiscount->date = now();
            // You can set other fields like date, status, reason, etc. here

            $userDiscount->save();

            // Customize success message based on language
            $successMessage = $lang === 'ar' ? 'تمت إضافة خصم المستخدم بنجاح' : 'User discount added successfully';

            return response()->json(['message' => $successMessage, 'after_discount' => $afterDiscount]);
        } catch (\Exception $e) {
            // Handle validation or other errors
            return response()->json(['error' => 'Invalid JSON data or internal server error'], 400);
        }
    }





    public function destroy(UserDiscount $userDiscount)
    {
        $userDiscount->delete();

        return redirect()->route('user_discounts.create')->with('success', 'User discount deleted successfully.');
    }



    // /* ------------------------------- Api methos ------------------------------- */
    /**
     * Get all user discounts through API.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUserDiscounts(Request $request)
    {
        try {
            $userDiscounts = UserDiscount::with(['store:id,name', 'discount:id,category'])
                ->where('user_id', auth()->user()->id)
                ->get();
            $lang = $request->json('lang');

            // Initialize variables for counts, total discount, and total savings
            $totalDiscount = 0;
            $totalSavings = 0;
            $totalDiscountsCount = $userDiscounts->count();
            $approvedCount = $userDiscounts->where('status', 1)->count();
            $rejectedCount = $userDiscounts->where('status', 2)->count();
            $pendingCount = $userDiscounts->where('status', 3)->count();

            $formattedUserDiscounts = $userDiscounts->map(function ($userDiscount) use ($lang, &$totalDiscount, &$totalSavings) {
                // Extract hour from created_at and format it in 12-hour format with AM or PM
                $hour = date('h:i A', strtotime($userDiscount->created_at));

                // Map status based on language
                $statusMap = [
                    3 => ($lang === 'ar' ? 'في انتظار التاكيد' : 'Pending Confirmation'),
                    2 => ($lang === 'ar' ? 'مرفوض' : 'Rejected'),
                    1 => ($lang === 'ar' ? 'مقبول' : 'Accepted'),
                ];

                // Initialize savings before the if statement
                $savings = 0;

                // If the discount is approved, add its amount to the total discount and calculate savings
                
                // if ($userDiscount->status == 1) {
                    $totalDiscount += $userDiscount->after_discount;
                    $savings = $userDiscount->total_payment - $userDiscount->after_discount;
                    $totalSavings += $savings;
                // }

                return [
                    'id' => $userDiscount->id,
                    'store_id' => $userDiscount->store_id,
                    'user_id' => $userDiscount->user_id,
                    'discount_id' => $userDiscount->discount_id,
                    'total_payment' => number_format($userDiscount->total_payment, 2),
                    'after_discount' => number_format($userDiscount->after_discount, 2), // Format to 2 decimal places
                    'date' => $userDiscount->date,
                    'status' => $statusMap[$userDiscount->status], // Map status
                    'reason' => $userDiscount->reason,
                    'obtained_status' => $userDiscount->obtained_status,
                    'obtained' => $userDiscount->obtained,
                    'notes' => $userDiscount->notes,
                    'store_name' => $userDiscount->store->name, // Add store name
                    'discount_category' => $userDiscount->discount->category, // Add discount category
                    'hour' => $hour, // Add hour
                    'created_at' => $userDiscount->created_at,
                    'updated_at' => $userDiscount->updated_at,
                    'savings' => number_format($savings, 2), // Format savings to 2 decimal places
                ];
            });

            // Format the total discount and total savings to 2 decimal places
            $totalDiscount = number_format($totalDiscount, 2);
            $totalSavings = number_format($totalSavings, 2);

            return response()->json([
                'user_discounts' => $formattedUserDiscounts,
                'total_discount' => $totalDiscount,
                'total_savings' => $totalSavings,
                'total_discounts_count' => $totalDiscountsCount,
                'approved_count' => $approvedCount,
                'rejected_count' => $rejectedCount,
                'pending_count' => $pendingCount,
            ], 200);
        } catch (\Exception $e) {
            // Log the exception for debugging
            // \Log::error($e);

            // Return a more detailed error response
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
