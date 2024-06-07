<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceRedirect extends Model
{
    use HasFactory;
    protected $fillable = [
        'device_type',
        'redirect_count',
        'app_store_link'
    ];
}
