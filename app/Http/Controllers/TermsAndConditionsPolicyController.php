<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Terms;

class TermsAndConditionsPolicyController extends Controller
{
    public function getTermsAndConditions(Request $request)
    {
        $userType = $request->input('user_type'); // assuming 'user_type' is sent in the request
        $language = $request->input('lang'); // assuming 'lang' is sent in the request
    
        // Define default response if no matching policy is found
        $response = "No terms and conditions found.";
    
        // Switch case to handle different user types
        switch ($userType) {
            case 'user':
                $policyType = 'user';
                break;
            case 'vendor':
                $policyType = 'vendor';
                break;
            default:
                $policyType = ''; // Handle default case if needed
                break;
        }
    
        // If policy type is not empty, fetch the corresponding terms and conditions
        if (!empty($policyType)) {
            $termsAndConditionsPolicy = Terms::where('type', $policyType)->first();
    
            // Debugging statement to check the value of $termsAndConditionsPolicy->content
            dd($termsAndConditionsPolicy->content);
    
            // Check language and set the response accordingly
            if ($termsAndConditionsPolicy) {
                $response = json_decode($termsAndConditionsPolicy->content, true); // Decode JSON content
            }
        }
    
        return response()->json(['terms_and_conditions' => $response]);
    }
    
    public function updateTermsAndConditions(Request $request)
    {
        $userType = $request->input('user_type'); // assuming 'user_type' is sent in the request
        $newArabicContent = $request->input('arabic_content');
        $newEnglishContent = $request->input('english_content');
        $language = $request->input('lang'); // assuming 'lang' is sent in the request

        // Validate inputs as needed

        // Define default response
        $response = "Terms and conditions updated successfully.";

        // Define the response message in Arabic if the selected language is Arabic
        $arabicResponse = "تم تحديث شروط وأحكام الاستخدام بنجاح.";

        // Switch case to handle different user types
        switch ($userType) {
            case 'user':
                $policyType = 'user';
                break;
            case 'vendor':
                $policyType = 'vendor';
                break;
            default:
                $policyType = ''; // Handle default case if needed
                break;
        }

        // If policy type is not empty, update the terms and conditions
        if (!empty($policyType)) {
            $termsAndConditionsPolicy = Terms::where('type', $policyType)->first();

            // Check if the policy exists
            if ($termsAndConditionsPolicy) {
                // Update both Arabic and English content
                $termsAndConditionsPolicy->arabic_content = $newArabicContent;
                $termsAndConditionsPolicy->english_content = $newEnglishContent;
                $termsAndConditionsPolicy->save();
            } else {
                // Create a new policy if it doesn't exist
                Terms::create([
                    'type' => $policyType,
                    'arabic_content' => $newArabicContent,
                    'english_content' => $newEnglishContent,
                ]);
            }
        } else {
            $response = "Invalid user type.";
        }

        // Set the response message based on the selected language
        if ($language == 'ar') {
            $response = $arabicResponse;
        }

        return response()->json(['message' => $response]);
    }

}
