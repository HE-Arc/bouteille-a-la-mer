<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as AuthUser;

class User extends AuthUser
{
    public $timestamps = false;
    protected $table = "users";

    protected $fillable = [
        "id",
        "username",
        "password"
    ];
}
