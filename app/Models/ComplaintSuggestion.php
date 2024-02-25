<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintSuggestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'is_vendor',
        'title',
        'description',
        'attachments',
        'additional_phone',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}