<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TermsAndConditionsPolicy extends Model
{
    protected $table = 'terms_and_conditions_policy';

    protected $fillable = [
        'type', 'english_content', 'arabic_content'
    ];
}
