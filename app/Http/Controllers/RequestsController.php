<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;
use App\Models\StoreCategory;
use App\Models\Region; // Import the Region model
use App\Models\WebsiteManager;
use App\Models\City;
use Illuminate\Validation\ValidationException;
use App\Models\Discount;
use App\Models\Request as StoreRequest;
use Carbon\Carbon;

class RequestsController extends Controller
{
    public function MyRequests(Request $request)
    {
        // Ensure the user is authenticated

        // Retrieve request by ID
        $requestId = $request->input('id');
        $action = $request->input('action');
        // Check if the request exists
        if (!$requestId || !$action) {
            return response()->json(['message' => 'Request ID and action are required'], 400);
        }
        
        $request = StoreRequest::find($requestId);

        if (!$request) {
            return response()->json(['message' => 'Request not found'], 404);
        }
    
        // Check if the request is already approved or not
        if ($request->approved) {
            return response()->json(['message' => 'Request is already approved'], 400);
        }

        // Check if the request is approved
        if ($action === '1') {
            // Check the type of request
            switch ($request->type) {
                case 'update_store':
                    // Update the corresponding row in the "stores" table
                    $storeId = $request->store_id;
                    $store = Store::find($storeId);
                    
                    if ($store) {
                        // Update store attributes using data from the "data" column
                        $storeData = json_decode($request->data, true);

                        // Example: Updating store name
                        $store->name = $storeData['name'];
                        $store->photo = $storeData['photo']; // Assuming photo is a nullable field
                        $store->work_days = json_decode($storeData['work_days'], true); // Assuming work_days is stored as JSON in the database
                        $store->latitude = $storeData['latitude'];
                        $store->longitude = $storeData['longitude'];
                        $store->tax_number = $storeData['tax_number'];
                        $store->category_id = $storeData['category_id'];
                        $store->region = $storeData['region'];

                        // Update other attributes accordingly

                        // Save the changes
                        $store->save();

                        return response()->json(['message' => 'Store updated successfully']);
                    } else {
                        return response()->json(['message' => 'Store not found'], 404);
                    }
                    break;
                case 'delete_discount':
                    // Check if the data is valid JSON
                    $data = json_decode($request->data, true);

                    if (!$data || !isset($data['discount_id'])) {
                        return response()->json(['message' => 'Invalid JSON data for delete_discount'], 400);
                    }

                    // Retrieve the discount by ID
                    $discountId = $data['discount_id'];
                    $discount = Discount::find($discountId);
                    

                    if ($discount) {
                        // Set is_deleted to 1
                        $discount->is_deleted = 1;

                        // Save the changes
                        $discount->save();

                        return response()->json(['message' => 'Discount deleted successfully']);
                    } else {
                        return response()->json(['message' => 'Discount not found'], 404);
                    }
                    break;
                case 'create_store':
                    // Retrieve the store associated with the request
                    $store = Store::find($request->store_id);
                    

                    // Check if the store exists
                    if ($store) {
                        // Set the verification column to 1 for the approved store
                        $store->verifcation = 1;

                        // Save the changes
                        $store->save();

                        return response()->json(['message' => 'Store verification set to 1']);
                    } else {
                        return response()->json(['message' => 'Store not found'], 404);
                    }
                    break;
                case 'delete_store':
                    // Retrieve the store associated with the request
                    $store = Store::find($request->store_id);
                    

                    // Check if the store exists
                    if ($store) {
                        // Delete the store
                        // $store->delete();
                        $store->is_deleted = 1;
                        return response()->json(['message' => 'Store deleted successfully']);
                    } else {
                        return response()->json(['message' => 'Store not found'], 404);
                    }
                    break;




                    // Add more cases for other types if needed

                default:
                    return response()->json(['message' => 'Unsupported request type'], 400);
            }
            $request->approved = true;
            $request->save();
            return response()->json(['message' => 'Request approved successfully']);
        }  elseif ($action === '2') {
            // Not approve the request
            // You can perform additional actions here if needed
            $request->approved = false;
            $request->save();
            return response()->json(['message' => 'Request not approved']);
        } else {
            // Invalid action
            return response()->json(['message' => 'Invalid action'], 400);
        }
    }
}
