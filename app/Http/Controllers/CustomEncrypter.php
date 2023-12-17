<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Http\Request;

class CustomEncrypter extends Controller
{
    public function customEncrypt($storeID)
    {
        // Your custom encryption logic
        $encryptedStoreID = 'SA' . str_pad($storeID, 10, '0', STR_PAD_LEFT) . 'VVDV';
    
        return $encryptedStoreID;
    }
    
    public function customDecrypt($encryptedStoreID)
    {
        // Your custom decryption logic
        // Extract the numeric part and remove the prefix and suffix
        $numericPart = substr($encryptedStoreID, 2, -4);
        $storeID = ltrim($numericPart, '0');
    
        return $storeID;
    }
}
