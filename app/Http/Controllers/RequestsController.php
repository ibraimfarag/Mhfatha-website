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
use Illuminate\Support\Facades\Http;

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
                    $user = User::find($request->user_id);
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
                        $request->approved = 1;
                        $request->save();

                        // Save the changes
                        $store->save();
                        // Send notification to the user
                        $notificationParams = [
                            'action' => 'sendToUser',
                            'recipient_identifier' => $request->user_id,
                        ];

                        // Check if the user's language is Arabic
                        if ($user->lang === 'ar') {
                            // If user's language is Arabic, set the title and body in Arabic
                            $notificationParams['body'] = 'تم الموافقة على طلب تحديث متجرك.';
                            $notificationParams['title'] = 'تحديث المتجر';
                        } else {
                            // If user's language is not Arabic, set the title and body in English or default language
                            $notificationParams['body'] = '1Your store update request has been approved.';
                            $notificationParams['title'] = 'Store Update';
                        }

                        // Call the sendNotification method
                        $this->sendNotification(new Request($notificationParams));

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
                    $user = User::find($request->user_id);


                    if ($discount) {
                        // Set is_deleted to 1
                        $discount->is_deleted = 1;

                        // Save the changes
                        $discount->save();
                        $request->approved = 1;
                        $request->save();

                        $notificationParams = [
                            'action' => 'sendToUser',
                            'recipient_identifier' => $request->user_id,
                        ];

                        // Check if the user's language is Arabic
                        if ($user->lang === 'ar') {
                            // If user's language is Arabic, set the title and body in Arabic
                            $notificationParams['body'] = 'لقد تم الموافقه على حذف الخصم الخاص بك.';
                            $notificationParams['title'] = 'حذف خصم المتجر';
                        } else {
                            // If user's language is not Arabic, set the title and body in English or default language
                            $notificationParams['body'] = 'You have been approved to delete your discount.';
                            $notificationParams['title'] = 'Store Discount Deletion';
                        }
                        // Call the sendNotification method
                        $this->sendNotification(new Request($notificationParams));

                        return response()->json(['message' => 'Discount deleted successfully']);
                    } else {
                        return response()->json(['message' => 'Discount not found'], 404);
                    }
                    break;
                case 'create_store':
                    // Retrieve the store associated with the request
                    $store = Store::find($request->store_id);
                    $user = User::find($request->user_id);


                    // Check if the store exists
                    if ($store) {
                        // Set the verification column to 1 for the approved store
                        $store->verifcation = 1;
                        $request->approved = 1;
                        $request->save();
                        // Save the changes
                        $store->save();

                        $notificationParams = [
                            'action' => 'sendToUser',
                            'recipient_identifier' => $request->user_id,
                        ];

                        // Check if the user's language is Arabic
                        if ($user->lang === 'ar') {
                            // If user's language is Arabic, set the title and body in Arabic
                            $notificationParams['body'] = 'لقد تم التحقق من متجرك.';
                            $notificationParams['title'] = 'تحقق من المتجر';
                        } else {
                            // If user's language is not Arabic, set the title and body in English or default language
                            $notificationParams['body'] = 'Your store has been verified.';
                            $notificationParams['title'] = 'verify store';
                        }
                        // Call the sendNotification method
                        $this->sendNotification(new Request($notificationParams));

                        return response()->json(['message' => 'Store verification set to 1']);
                    } else {
                        return response()->json(['message' => 'Store not found'], 404);
                    }
                    break;
                case 'delete_store':
                    // Retrieve the store associated with the request
                    $store = Store::find($request->store_id);
                    $user = User::find($request->user_id);


                    // Check if the store exists
                    if ($store) {
                        // Delete the store
                        // $store->delete();
                        $store->is_deleted = 1;
                        $notificationParams = [
                            'action' => 'sendToUser',
                            'recipient_identifier' =>  $request->user_id, // Assuming user_id is the user associated with the store
                            'body' => 'Your store has been deleted.',
                            'title' => 'Store Deletion'
                        ];

                        $notificationParams = [
                            'action' => 'sendToUser',
                            'recipient_identifier' => $request->user_id,
                        ];

                        // Check if the user's language is Arabic
                        if ($user->lang === 'ar') {
                            // If user's language is Arabic, set the title and body in Arabic
                            $notificationParams['body'] = 'لقد تم الموافقه على حذف متجرك.';
                            $notificationParams['title'] = 'حذف المتجر';
                        } else {
                            // If user's language is not Arabic, set the title and body in English or default language
                            $notificationParams['body'] = 'You have been approved to delete your store.';
                            $notificationParams['title'] = 'Store Deletion';
                        }


                        // Call the sendNotification method
                        $this->sendNotification(new Request($notificationParams));
                        return response()->json(['message' => 'Store deleted successfully']);
                    } else {
                        return response()->json(['message' => 'Store not found'], 404);
                    }
                    break;




                    // Add more cases for other types if needed

                default:
                    return response()->json(['message' => 'Unsupported request type'], 400);
            }
            $request->approved = 1;
            $request->save();
            return response()->json(['message' => 'Request approved successfully']);
        } elseif ($action === '2') {
            // Not approve the request
            // You can perform additional actions here if needed
            $request->approved = 2;
            $request->save();
            return response()->json(['message' => 'Request not approved']);
        } else {
            // Invalid action
            return response()->json(['message' => 'Invalid action'], 400);
        }
    }
    public function sendNotification(Request $request)
    {
        // Retrieve action, body, title, and recipient information from the request body
        $action = $request->input('action');
        $body = $request->input('body');
        $title = $request->input('title');
        $bodyAr = $request->input('body_ar');
        $titleAr = $request->input('title_ar');
        $recipientIdentifier = $request->input('recipient_identifier'); // This could be user ID, email, or mobile

        // Initialize a new Google_Client
        $client = new \Google\Client();

        // Set the authentication configuration using the provided JSON data
        $client->setAuthConfig(public_path('firebase/mhfaata.json'));

        // Add the necessary scope for Firebase Messaging
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        // Use application default credentials
        $client->useApplicationDefaultCredentials();

        // Get the URL for sending messages to FCM
        $apiUrl = 'https://fcm.googleapis.com/v1/projects/mhfaata/messages:send';

        // Obtain an access token
        $client->fetchAccessTokenWithAssertion();

        // Get the access token
        $accessToken = $client->getAccessToken()['access_token'];

        // Initialize response variable
        $response = null;

        // Select the case based on the provided action
        switch ($action) {
            case 'sendToUser':
                // Find the user based on the provided identifier (ID, email, or mobile)
                $user = $this->getUserByRecipientIdentifier($recipientIdentifier);

                if (!$user) {
                    return response()->json(['error' => 'User not found'], 404);
                }
                $lang = $user->lang ?? 'en';

                if ($lang === 'ar') {
                    $response = $this->sendNotificationToUser($user, $accessToken, $apiUrl, $bodyAr, $titleAr);
                } else {
                    $response = $this->sendNotificationToUser($user, $accessToken, $apiUrl, $body, $title);
                }
                break;

                case 'sendByFilters':
                    // Get users based on filters
                    $filteredUsersQuery = User::query();
                
                    if ($request->has('gender')) {
                        $filteredUsersQuery->where('gender', $request->input('gender'));
                    }
                
                    if ($request->has('birthday')) {
                        $filteredUsersQuery->where('birthday', $request->input('birthday'));
                    }
                
                    if ($request->has('region')) {
                        $filteredUsersQuery->where('region', $request->input('region'));
                    }
                
                    if ($request->has('is_vendor')) {
                        $filteredUsersQuery->where('is_vendor', $request->input('is_vendor'));
                    }
                
                    if ($request->has('is_admin')) {
                        $filteredUsersQuery->where('is_admin', $request->input('is_admin'));
                    }
                
                    if ($request->has('platform')) {
                        $filteredUsersQuery->where('platform', $request->input('platform'));
                    }
                
                    // Retrieve filtered users
                    $filteredUsers = $filteredUsersQuery->get();
                
                    // Check if any users match the filters
                    if ($filteredUsers->isEmpty()) {
                        return response()->json(['message' => 'No users match the provided filters'], 404);
                    }
                
                    // Send notifications to each filtered user
                    foreach ($filteredUsers as $user) {
                        // Determine language
                        $lang = $user->lang ?? 'en';
                
                        if ($lang === 'ar') {
                            $this->sendNotificationToUser($user, $accessToken, $apiUrl, $bodyAr, $titleAr);
                        } else {
                            $this->sendNotificationToUser($user, $accessToken, $apiUrl, $body, $title);
                        }
                    }
                
                    // Return success message and list of filtered users
                    return response()->json(['message' => 'Notifications sent successfully', 'users' => $filteredUsers]);
                    break;
                

            default:
                return response()->json(['error' => 'Invalid action'], 400);
        }

        // Return the response
        return $response;
    }
    private function getUserByRecipientIdentifier($identifier)
    {
        // Check if the identifier is numeric and has 10 digits (assuming it's a mobile number)
        if (is_numeric($identifier) && strlen($identifier) == 10) {
            // Search for the user by mobile number
            $user = User::where('mobile', $identifier)->first();
        }
        // Check if the identifier is numeric and less than 10 digits (assuming it's an ID)
        elseif (is_numeric($identifier) && strlen($identifier) < 10) {
            // Search for the user by ID
            $user = User::find($identifier);
        } else {
            // Search for the user by email
            $user = User::where('email', $identifier)->first();
        }

        return $user;
    }


    private function sendNotificationToUsers($users, $accessToken, $apiUrl, $body, $title)
    {
        $responses = [];

        foreach ($users as $user) {
            $response = $this->sendNotificationToUser($user, $accessToken, $apiUrl, $body, $title);
            $responses[] = $response;
        }

        return $responses;
    }

    private function sendNotificationToUser($user, $accessToken, $apiUrl, $body, $title)
    {
        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken,
        ])->post($apiUrl, [
            'message' => [
                'token' => $user->device_token,
                'notification' => [
                    'body' => $body,
                    'title' => $title,
                ],
            ],
        ]);

        return $response;
    }
}
