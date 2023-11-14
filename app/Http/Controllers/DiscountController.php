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
    }    public function index_api(Store $store, Request $request)
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
