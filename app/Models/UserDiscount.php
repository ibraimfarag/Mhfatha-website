<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDiscount extends Model
{
    protected $fillable = [
        'store_id',
        'user_id',
        'discount_id',
        'total_payment',
        'after_discount',
        'date',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_id');
    }
}
