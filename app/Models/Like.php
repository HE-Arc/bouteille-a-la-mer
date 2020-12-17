<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    public $timestamps = false;
    protected $table = "likes_relation";

    protected $fillable = [
        'user', 
        'message',
    ];
}
