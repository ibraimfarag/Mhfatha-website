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


class ComplaintsSuggestionsParentController extends Controller
{
    public function store(Request $request)
    {
        // Fetch the authenticated user's ID
        $userId = Auth::id();

        // Generate a random ticket number
        $ticketNumber = 'MH-' . str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT);

        // Validate the incoming request data
        $validator = Validator::make($request->all(), [
            'option_id' => 'exists:complaints_suggestions_option,id',
            'parent_id' => 'exists:complaints_suggestions_parent,id',
            'store_id' => 'nullable|exists:stores,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'is_vendor' => 'boolean',
            'description' => 'array', // Ensure description is an array
            'description.message_type' => 'string',
            'description.message' => 'string',
            'description.read' => 'nullable|boolean', // Add read as a boolean
            'description.date' => 'string',
            'description.attached' => 'nullable',
            'status' => 'nullable|in:read,unread,under processer,closed',
            'attachments' => 'nullable|string',
            'additional_phone' => 'nullable|string',
            'lang' => 'nullable|in:en,ar', // Validate lang input
        ]);

        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }
        $description = $request->description;
        if (!isset($description['message_type'])) {
            $description['message_type'] = 'client';
        }
        if (!isset($description['date'])) {
            $description['date'] = now()->toDateTimeString();
        }
        // Set the default read value if not provided
        if (!isset($description['read'])) {
            $description['read'] = false;
        }

        // Handle file upload for description.attached
        if ($request->hasFile('description.attached')) {
            $file = $request->file('description.attached');
            $folderPath = public_path('frontend/assets/' . $ticketNumber);
            // Create directory if it doesn't exist
            if (!File::exists($folderPath)) {
                File::makeDirectory($folderPath, 0777, true, true);
            }
            $imageName = $ticketNumber . '-' . now()->format('YmdHis') . '.' . $file->getClientOriginalExtension();
            $file->move($folderPath, $imageName);
            $description['attached'] = 'frontend/assets/' . $ticketNumber . '/' . $imageName;
        } else {
            $description['attached'] = null;
        }

        // Encode the description as JSON
        $descriptionJson = json_encode($request->description);

        // Create a new ComplaintsSuggestions instance and save it
        $complaintsSuggestions = new ComplaintSuggestion([
            'option_id' => $request->option_id,
            'parent_id' => $request->parent_id,
            'user_id' => $userId, // Assign the user ID
            'store_id' => $request->store_id,
            'discount_id' => $request->discount_id,
            'is_vendor' => $request->is_vendor,
            'description' => $descriptionJson, // Save the description as JSON
            'status' => $request->status,
            'ticket_number' => $ticketNumber, // Assign the generated ticket number
            'attachments' => $request->attachments,
            'additional_phone' => $request->additional_phone,
        ]);

        $complaintsSuggestions->save();

      // Return the response based on the lang input
    if ($request->lang === 'ar') {
        return response()->json(['message' => 'لقد تم تسجيل الطلب بنجاح برقم ' ,'ticketNumber' => $ticketNumber], 201);
    } else {
        return response()->json(['message' => 'Complaints/Suggestions created successfully with ticket number ' ,'ticketNumber' => $ticketNumber], 201);
    }
    }

    public function getComplaintsSuggestionsOptions(Request $request)
    {
        $criteria = $request->get('criteria', 'all');


        switch ($criteria) {
            case 'vendor':
            default:

                $options = ComplaintsSuggestionsOption::where('parent_id', '2')->get();



                break;

            case 'app':

                $options = ComplaintsSuggestionsOption::where('parent_id', '3')->get();

                break;
        }



        return response()->json($options, 200);
    }


}
