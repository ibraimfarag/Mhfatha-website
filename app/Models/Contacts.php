<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contacts extends Model
{
    use HasFactory;
    protected $fillable = [
        // Add the new columns to the $fillable array
        'whatsapp',
        'email',
        'type',
    ];
}
