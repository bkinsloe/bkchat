<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chats extends Model
{
    public function get_chat($chat_id)
    {
      $chat = DB::table('chats')->where('id', $chat_id)->first();
      return $chat;
    }
}
