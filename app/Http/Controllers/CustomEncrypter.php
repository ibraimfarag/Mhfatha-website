<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Http\Request;

class CustomEncrypter extends Controller
{
    public function customEncode($data)
    {
        // Base64 encode the data
        $encodedData = base64_encode($data);
    
        // Trim the result to 12 characters
        $trimmedData = substr($encodedData, 0, 12);
    
        return $trimmedData;
    }
    
    public function customDecode($encodedData)
    {
        // Pad the encoded data with "=" to make its length a multiple of 4
        $paddedData = str_pad($encodedData, strlen($encodedData) + (4 - strlen($encodedData) % 4) % 4, '=');
    
        // Base64 decode the padded data
        $decodedData = base64_decode($paddedData);
    
        return $decodedData;
    }
    
}
