<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use app\Models\UserDiscount;

use Illuminate\Http\Request;

class UserDiscountController extends Controller
{
    public function create()
    {
        return view('user_discounts.create');
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
