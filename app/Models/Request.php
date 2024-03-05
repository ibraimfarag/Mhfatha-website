<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Request extends Model
{

    use HasFactory;

    protected $fillable = ['user_id', 'store_id', 'type', 'data', 'approved'];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id'); // Assuming user_id is the foreign key in the requests table
    }
    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id'); // Assuming store_id is the foreign key in the requests table
    }
}
