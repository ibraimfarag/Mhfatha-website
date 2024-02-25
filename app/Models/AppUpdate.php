<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'ios_version',
        'ios_required',
        'android_version',
        'android_required',
    ];
}
