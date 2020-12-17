<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Message extends Model
{
    public $timestamps = false;
    protected $table = "messages";

    protected static function booted()
    {
        static::deleted(function ($message) {
            if($message->image !== NULL)
            {
                $path = public_path('uploads/').$message->image;
                dump('deleting ' . $path);
                if (File::exists($path))
                    File::delete($path);

            };
        });
    }
}
