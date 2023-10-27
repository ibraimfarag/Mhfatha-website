<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use app\Models\Store;

use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $stores = $user->stores; // Retrieve stores associated with the logged-in user
        return view('stores.index', compact('stores'));
    }

    public function edit(Store $store)
    {
        // Check if the logged-in user is the owner of the store and is a vendor
        if (Auth::user()->id !== $store->user_id || !Auth::user()->is_vendor) {
            abort(403, 'Unauthorized action.');
        }
    
        return view('stores.edit', compact('store'));
    }
    
    public function update(Request $request, Store $store)
    {
        // Check if the logged-in user is the owner of the store and is a vendor
        if (Auth::user()->id !== $store->user_id || !Auth::user()->is_vendor) {
            abort(403, 'Unauthorized action.');
        }
    
        $data = $request->validate([
            // Define validation rules for store data updates
            'name' => 'required|string',
            'location' => 'required|string',
            // Add more fields as needed
        ]);
    
        $store->update($data);
    
        return redirect()->route('stores.index')->with('success', 'Store updated successfully.');
    }
    
    public function destroy(Store $store)
    {
        // Check if the logged-in user is the owner of the store and is a vendor
        if (Auth::user()->id !== $store->user_id || !Auth::user()->is_vendor) {
            abort(403, 'Unauthorized action.');
        }
    
        $store->delete();
    
        return redirect()->route('stores.index')->with('success', 'Store deleted successfully.');
    }
    }
