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
        // Validate the input data
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'store_id' => 'required|integer',
            'discount_id' => 'required|integer',
            'total_payment' => 'required|numeric',
        ]);
    
        // Calculate the after_discount based on the discount percentage
        $discount = Discount::find($validatedData['discount_id']);
    
        if (!$discount) {
            return response()->json(['message' => 'Discount not found'], 404);
        }
    
        $percent = $discount->percent;
        $totalPayment = $validatedData['total_payment'];
    
        $afterDiscount = $totalPayment * (1 - $percent / 100);
    
        // Create a new user discount entry
        $userDiscount = new UserDiscount();
        $userDiscount->user_id = $validatedData['user_id'];
        $userDiscount->store_id = $validatedData['store_id'];
        $userDiscount->discount_id = $validatedData['discount_id'];
        $userDiscount->total_payment = $totalPayment;
        $userDiscount->after_discount = $afterDiscount;
        $userDiscount->date = now(); 
        // You can set other fields like date, status, reason, etc. here
    
        $userDiscount->save();
    
        return response()->json(['message' => 'User discount added successfully', 'after_discount' => $afterDiscount]);
    }
    


    

    public function destroy(UserDiscount $userDiscount)
    {
        $userDiscount->delete();

        return redirect()->route('user_discounts.create')->with('success', 'User discount deleted successfully.');
    }
}
