<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $fillable = [
        'store_id',
        'percent',
        'category',
        'start_date',
        'end_date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
}
