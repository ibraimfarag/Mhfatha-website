<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Terms extends Model
{
    protected $table = 'terms_and_conditions_policy';
    use HasFactory;

    protected $fillable = [
        'type', 'english_content', 'arabic_content'
    ];
}
