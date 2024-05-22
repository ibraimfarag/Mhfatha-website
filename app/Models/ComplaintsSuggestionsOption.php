<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintsSuggestionsOption extends Model
{
    use HasFactory;
    protected $table = 'complaints_suggestions_option'; // Explicitly specify the table name

    protected $fillable = [
        'option_ar',
        'option_en',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(ComplaintsSuggestionsParent::class, 'parent_id');
    }
}
