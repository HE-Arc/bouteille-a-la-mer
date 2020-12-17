<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Message;

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

    protected static function booted()
    {
        static::deleted(function ($conv) {
            echo 'delete conv\n';
            $messages = Message::where(['parent' => $conv->id])->get();     

            foreach($messages as $msg) {
                $msg->delete();
            }
        });
    }
}
