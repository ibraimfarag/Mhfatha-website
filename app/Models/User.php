<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;  //add the namespace

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'gender',
        'birthday',
        'city',
        'region',
        'mobile',
        'email',
        'photo',
        'is_vendor',
        'is_admin',
        'password',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function stores()
    {
        return $this->hasMany(Store::class);
    }
    public function userDiscounts()
    {
        return $this->hasMany(UserDiscount::class);
    }
}
