<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintsSuggestionsParent extends Model
{
    use HasFactory;
    protected $table = 'complaints_suggestions_parent'; // Explicitly specify the table name

    protected $fillable = [
        'parent_ar',
        'parent_en',
    ];

    public function options()
    {
        return $this->hasMany(ComplaintsSuggestionsOption::class, 'parent_id');
    }

}
