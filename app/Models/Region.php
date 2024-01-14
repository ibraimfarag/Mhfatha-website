<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        'region_ar',
        'region_en',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }
}
