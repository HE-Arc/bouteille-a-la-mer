<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    public $timestamps = false;
    protected $table = "conversations";

    protected $fillable = [
        'radius', 
        'time_of_death', 
        'lat' ,
        'long',
        'author'
    ];
}
