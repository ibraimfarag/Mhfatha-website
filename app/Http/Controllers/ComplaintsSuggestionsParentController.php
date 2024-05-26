<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\ComplaintSuggestion;
use App\Models\ComplaintsSuggestionsParent;
use App\Models\ComplaintsSuggestionsOption;

use Illuminate\Support\Facades\Auth;
use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\JsonResponse;
use App\Models\StoreCategory;
use App\Models\Region; // Import the Region model
use App\Models\WebsiteManager;
use App\Models\TermsAndConditionsPolicy;
use App\Models\City;
use Illuminate\Validation\ValidationException;
use App\Models\Discount;
use App\Models\Request as StoreRequest;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;
use  App\Models\UserDiscount;
use Illuminate\Support\Arr;


class ComplaintsSuggestionsParentController extends Controller
{

    public function store(Request $request)
    {
        // Fetch the authenticated user's ID
        $userId = Auth::user()->id;
        $isVendor = Auth::user()->is_vendor;
    
        // Generate a random ticket number
        $ticketNumber = 'MH-' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);
    
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'option_id' => 'nullable|exists:complaints_suggestions_option,id',
            'parent_id' => 'nullable|exists:complaints_suggestions_parent,id',
            'store_id' => 'nullable|exists:stores,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'is_vendor' => 'nullable|boolean',
            'description' => 'nullable|array', // Ensure description is an array
            'description.*.message_type' => 'nullable|string',
            'description.*.message' => 'nullable|string',
            'description.*.read' => 'nullable|boolean', // Add read as a boolean
            'description.*.date' => 'nullable|string',
            'description.*.attached' => 'nullable|array',
            'status' => 'nullable|in:read,unread,under processer,closed',
            'attachments' => 'nullable|string',
            'additional_phone' => 'nullable|string',
            'lang' => 'nullable|in:en,ar', // Validate lang input
        ]);
    
        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $status = 'under processer';
        $parent_id = null;
        if ($request->has('option_id')) {
            $parent_id = ComplaintsSuggestionsOption::where('id', $request->option_id)
                ->value('parent_id');
        }
        $description = $request->input('description', []);
        $formattedDescriptions = [];
    
        // Loop through each description item and format it with numerical indices
        foreach ($description as $index => $desc) {
            $formattedDescriptions[] = [
                'message_type' => $desc['message_type'] ?? 'client',
                'message' => $desc['message'] ?? '',
                'date' => $desc['date'] ?? now()->toDateTimeString(),
                'read' => $desc['read'] ?? 0,
                'attached' => $desc['attached'] ?? [],
            ];
        }
    
        // Encode the formatted descriptions as JSON
        $descriptionJson = json_encode($formattedDescriptions, JSON_UNESCAPED_UNICODE);
        // Create a new ComplaintsSuggestions instance and save it
        $complaintsSuggestions = new ComplaintSuggestion([
            'option_id' => $request->option_id,
            'parent_id' => $parent_id,
            'user_id' => $userId, // Assign the user ID
            'store_id' => $request->store_id,
            'discount_id' => $request->discount_id,
            'is_vendor' => $isVendor,
            'description' => $descriptionJson, // Save the description as JSON
            'status' => $status,
            'ticket_number' => $ticketNumber, // Assign the generated ticket number
            'attachments' => $request->attachments,
            'additional_phone' => $request->additional_phone,
        ]);
    
        $complaintsSuggestions->save();
    
        // Return the response based on the lang input
        if ($request->lang === 'ar') {
            return response()->json(['message' => 'لقد تم تسجيل الطلب بنجاح برقم ', 'descriptions' => $formattedDescriptions], 201);
        } else {
            return response()->json(['message' => 'Complaints/Suggestions created successfully with ticket number ', 'descriptions' => $formattedDescriptions], 201);
        }
    }
    
    

    public function getComplaintsSuggestionsOptions(Request $request)
    {
        $criteria = $request->get('criteria', 'all');


        switch ($criteria) {
            case 'vendor':

                $options = ComplaintsSuggestionsOption::where('parent_id', '2')->get();



                break;

            case 'app':

                $options = ComplaintsSuggestionsOption::where('parent_id', '3')->get();

                break;


            default:
                $options = ComplaintsSuggestionsOption::where('parent_id', '!=', '2')->get();
                break;
        }



        return response()->json($options, 200);
    }

    /**
     * Get all ComplaintSuggestions for the authenticated user.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllUserComplaintSuggestions(Request $request)
    {
        // Fetch the authenticated user's ID
        $userId = Auth::user()->id;
        
        // Get the criteria and postId from the request
        $criteria = $request->input('criteria');
        $postId = $request->input('postId');
    
        switch ($criteria) {
            case 'id':
                // Get the specific complaint suggestion by ID
                $complaintSuggestions = ComplaintSuggestion::where('user_id', $userId)
                                                            ->where('id', $postId)
                                                            ->first();
                break;
                
            case 'all':
                default:
                // Retrieve all complaint suggestions
                $complaintSuggestions = ComplaintSuggestion::get();
                break;
                
            case 'user':
                // Retrieve complaint suggestions for the authenticated user
                $complaintSuggestions = ComplaintSuggestion::where('user_id', $userId)->get();
                break;
    
        
        }
    
        // Return the response
        return response()->json($complaintSuggestions, 200);
    }
    

    /**
     * Update a ComplaintSuggestion entry using JSON data.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id The ID of the ComplaintSuggestion to update
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // Fetch the authenticated user's ID
        $userId = Auth::user()->id;
        $lang = $request->input('lang', 'en'); // Default to 'en' if 'lang' is not provided
    
        // Extract the ID from the request data
        $id = $request->input('id');
        $criteria = $request->input('criteria', 'update'); // Default to 'update' if 'criteria' is not provided
    
        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:complaints_suggestions,id',
            'description.message_type' => 'nullable|string',
            'description.message' => 'nullable|string',
            'description.read' => 'nullable|boolean',
            'description.date' => 'nullable|string',
            'description.attached' => 'nullable|array',
            'status' => 'nullable|in:read,unread,under processer,closed',
            'attachments' => 'nullable|string',
            'additional_phone' => 'nullable|string',
        ]);
    
        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
    
        // Fetch the ComplaintSuggestion to update
        $complaintSuggestion = ComplaintSuggestion::findOrFail($id);
        $ticketNumber = $complaintSuggestion->ticket_number;
    
        switch ($criteria) {
            case 'fetch':
                // Return the fetched complaint suggestion
                return response()->json(['ticketNumber' => $ticketNumber, 'complaintSuggestion' => $complaintSuggestion], 200);
                break;
    
            case 'update':
                // Get the current description from the database
                $currentDescription = json_decode($complaintSuggestion->description, true);
    
                // Apply specific updates based on message_type
                switch ($request->input('description.message_type')) {
                    case 'support':
                        // Update message_type to 'support'
                        $newDescription['message_type'] = 'support';
                        break;
                    default:
                        // Update message_type to 'client'
                        $newDescription['message_type'] = 'client';
                        break;
                }
    
                // Merge the new values with the current description
                $newDescription = $request->input('description', []);
                $newDescription['message'] = $newDescription['message'] ?? ''; // Ensure message is included
                $newDescription['date'] = $newDescription['date'] ?? now()->toDateTimeString();
                $newDescription['read'] = $newDescription['read'] ?? 0;
    
                $attachedFiles = [];
    
                if ($request->hasFile('description.attached')) {
                    foreach ($request->file('description.attached') as $file) {
                        $folderPath = public_path('FrontEnd/assets/images/supporting/' . $ticketNumber);
                        // Create directory if it doesn't exist
                        if (!File::exists($folderPath)) {
                            File::makeDirectory($folderPath, 0777, true, true);
                        }
                        $imageName = $ticketNumber . '-' . now()->format('YmdHis') . '-' . $file->getClientOriginalName();
                        $file->move($folderPath, $imageName);
                        $attachedFiles[] = 'FrontEnd/assets/images/supporting/' . $ticketNumber . '/' . $imageName;
                    }
                }
    
                $newDescription['attached'] = $attachedFiles;
    
                // Append the new description to the existing array of descriptions
                $currentDescription[] = $newDescription;
    
                // Update the ComplaintSuggestion fields
                $complaintSuggestion->description = json_encode($currentDescription, JSON_UNESCAPED_UNICODE);
                $complaintSuggestion->fill($request->except(['id', 'description']));
                $complaintSuggestion->save();
    
                // Response messages
                $successMessage = $lang == 'ar' ? 'تم تحديث الشكوى بنجاح' : 'ComplaintSuggestion updated successfully';
                $unauthorizedMessage = $lang == 'ar' ? 'غير مصرح' : 'Unauthorized';
    
                // Return a success message
                return response()->json(['message' => $successMessage, 'ticketNumber' => $ticketNumber, 'complaintSuggestion' => $complaintSuggestion], 200);
                break;
    
            default:
                // Handle invalid criteria
                return response()->json(['error' => 'Invalid criteria'], 400);
                break;
        }
    }
    

    public function changeStatus(Request $request)
{
    // Fetch the authenticated user's ID
    $lang = $request->input('lang', 'en'); // Default to 'en' if 'lang' is not provided

    // Extract the ID from the request data
    $id = $request->input('id');
    $newStatus = $request->input('status');

    // Validate the incoming request data
    $validator = Validator::make($request->all(), [
        'id' => 'required|exists:complaints_suggestions,id',
        'status' => 'required|in:read,unread,under processer,closed',
    ]);

    // If validation fails, return the errors
    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    // Fetch the ComplaintSuggestion to update
    $complaintSuggestion = ComplaintSuggestion::where('id', $id)->first();
    $ticketNumber = $complaintSuggestion->ticket_number;

    // Ensure that the authenticated user owns the ComplaintSuggestion
    // if ($complaintSuggestion->user_id != $userId) {
    //     return response()->json(['error' => 'Unauthorized'], 401);
    // }

    // Update the status based on the new status provided
    switch ($newStatus) {
        case 'read':
        case 'unread':
        case 'under processer':
        case 'closed':
            // Update the status
            $complaintSuggestion->status = $newStatus;
            $complaintSuggestion->save();

            // Response messages
            $successMessage = $lang == 'ar' ? 'تم تحديث حالة الشكوى بنجاح' : 'ComplaintSuggestion status updated successfully';
            break;

        default:
            // If the provided status is not valid
            return response()->json(['error' => 'Invalid status provided'], 400);
    }

    // Return a success message
    return response()->json(['message' => $successMessage], 200);
}

}
