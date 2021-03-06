<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Chats extends Model {

    public function get_chat($chat_id, $user_id)
    {
      $chat = DB::table('chats')
                  ->where('id', $chat_id)
                  ->where('user_id', $user_id)
                  ->first();
      return $chat;
    }

    public function get_user_chats($user_id, $limit, $offset)
    {
      $chats = DB::table('chats')->where('user_id', $user_id)->offset($offset)->limit($limit)->get();
      return $chats;
    }

    public function get_total_user_chat_count($user_id)
    {
      $total_count = DB::table('chats')->where('user_id', $user_id)->count();
      return $total_count;
    }

    public function insert_chat($insert_array)
    {
      $chat_id = DB::table('chats')->insertGetId($insert_array);
      return $chat_id;
    }

    public function update_chat($id, $user_id, $name)
    {
      DB::table('chats')
            ->where('id', $id)
            ->where('user_id', $user_id)
            ->update(array('name' => $name));
    }
}
