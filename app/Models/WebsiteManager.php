<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebsiteManager extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_title',
        'site_description',
        'site_logo',
        'site_favicon',
        'site_meta_keywords',
        'site_meta_description',
        'site_status',
        'map_distance',
        'commission',
        'company_mail',
        'company_phone',
        'hero_intro_sliders',
        'who_are_we_title',
        'who_are_we_description',
        'who_are_we_image',
        'tell_public_story',
        'advantages_title',
        'advantages_description',
        'advantages_image',
        'advantages_tabs',
        'download_app_image',
        'download_app_description',
        'ios_link',
        'android_link',
    ];

    protected $casts = [
        'site_title' => 'json',
        'site_description' => 'json',
        'site_meta_keywords' => 'array',
        'site_meta_description' => 'json',
        'hero_intro_sliders' => 'json',
        'who_are_we_title' => 'json',
        'who_are_we_description' => 'json',
        'tell_public_story' => 'json',
        'advantages_title' => 'json',
        'advantages_description' => 'json',
        'advantages_tabs' => 'json',
        'download_app_description' => 'json',
    ];
    
}
