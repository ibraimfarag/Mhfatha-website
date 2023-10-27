<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use app\Models\Discount;

class DiscountController extends Controller
{
    public function create()
    {
        return view('discounts.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            // Define validation rules for discount creation
            'store_id' => 'required|exists:stores,id',
            'percent' => 'required|numeric',
            'category' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            // Add more fields as needed
        ]);

        Discount::create($data);

        return redirect()->route('discounts.create')->with('success', 'Discount created successfully.');
    }

    public function edit(Discount $discount)
    {
        return view('discounts.edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount)
    {
        $data = $request->validate([
            // Define validation rules for discount updates
            'percent' => 'required|numeric',
            'category' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            // Add more fields as needed
        ]);

        $discount->update($data);

        return redirect()->route('discounts.edit', $discount)->with('success', 'Discount updated successfully.');
    }

    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('discounts.create')->with('success', 'Discount deleted successfully.');
    }
}
