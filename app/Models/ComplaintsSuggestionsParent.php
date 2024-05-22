<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintsSuggestionsParent extends Model
{
    use HasFactory;
    protected $fillable = ['option_ar', 'option_en', 'parent_id'];

    public function options()
    {
        return $this->hasMany(ComplaintsSuggestionsOption::class, 'parent_id');
    }

}
