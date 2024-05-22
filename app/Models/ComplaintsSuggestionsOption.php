<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintsSuggestionsOption extends Model
{
    use HasFactory;
    protected $fillable = ['parent_ar', 'parent_en'];

    public function options()
    {
        return $this->hasMany(ComplaintsSuggestionsOption::class, 'parent_id');
    }

    public function option()
    {
        return $this->belongsTo(ComplaintsSuggestionsParent::class, 'parent_id');
    }
}
