<?php

namespace App\Models;
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_name_en',
        'category_name_ar',
    ];

    public function stores()
    {
        return $this->hasMany(Store::class, 'category_id');
    }
}

