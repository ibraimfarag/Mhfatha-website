<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'location',
        'phone',
        'url_map',
        'photo',
        'qr',
        'total_payments',
        'total_withdrawals',
        'count_times',
        'work_hours',
        'work_days',
        'city',
        'region',
        'latitude',
        'longitude',
        'status',
        'verifcation',
        'is_bann',
        'bann_msg',
        "is_deleted",
        
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }
    public function userDiscounts()
    {
        return $this->hasMany(UserDiscount::class);
    }



}
