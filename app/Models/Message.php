<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Message extends Model
{
    /*public function getMyConversations($myid) {
        $conv = DB::table('conversations')
            ->where('author', '=', $myid)
            ->join('messages', 'conversations.id', '=', 'messages.id')
            ->get();

    }*/
    public $timestamps = false;
    protected $table = "messages";
}
