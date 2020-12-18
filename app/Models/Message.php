<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use App\Models\Like;

class Message extends Model
{
    public $timestamps = false;
    protected $table = "messages";

    protected static function booted()
    {
        static::deleted(function ($message) {
            echo 'message delete\n';

            $likes = Like::where(['message' => $message->id])->get();
            foreach($likes as $like) {
                $like->delete();
            }

            if($message->image !== NULL)
            {
                $path = public_path().$message->image;
                dump('deleting ' . $path);
                if (File::exists($path))
                    File::delete($path);

            };
        });
    }
}
