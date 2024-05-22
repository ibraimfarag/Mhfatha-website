<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'option_id',
        'parent_id',
        'user_id',
        'store_id',
        'discount_id',
        'is_vendor',
        'description',
        'status',
        'ticket_number',
        'attachments',
        'additional_phone',
    ];

    public function option()
    {
        return $this->belongsTo(ComplaintsSuggestionsOption::class, 'option_id');
    }

    public function parent()
    {
        return $this->belongsTo(ComplaintsSuggestionsParent::class, 'parent_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }
}