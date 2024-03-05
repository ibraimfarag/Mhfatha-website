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
        'tax_number',
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

    public function category()
    {
        return $this->belongsTo(StoreCategory::class, 'category_id');
    }

    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
    public function requests()
    {
        return $this->hasMany(Request::class);
    }
}
