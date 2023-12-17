<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Http\Request;

class CustomEncrypter extends Controller
{
    public static function encrypt($storeID)
    {
        // Encrypt the store ID using Laravel's Crypt::encryptString
        $encryptedData = Crypt::encryptString($storeID);

        // Convert the encrypted data to uppercase and remove any non-alphanumeric characters
        $formattedEncryptedData = strtoupper(preg_replace('/[^A-Z0-9]/', '', $encryptedData));

        return $formattedEncryptedData;
    }

    public static function decrypt($formattedEncryptedData)
    {
        // Convert the formatted encrypted data to lowercase
        $encryptedData = strtolower($formattedEncryptedData);

        // Decrypt the data using Laravel's Crypt::decryptString
        $decryptedData = Crypt::decryptString($encryptedData);

        return $decryptedData;
    }
}
