<?php

namespace App\Models;
use Carbon\Carbon;

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
        'discounts_status',
        "is_deleted",

    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function setEndDateAttribute($value)
    {
        $this->attributes['end_date'] = $value;

        // Automatically update discounts_status based on the end_date
        $currentDate = Carbon::now();
        $endDate = Carbon::parse($value);

        if ($endDate <= $currentDate) {
            $this->attributes['discounts_status'] = 'end';
        } else {
            $this->attributes['discounts_status'] = 'working';
        }
    }

}
