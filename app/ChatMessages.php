<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class ChatMessages extends Model
{
  public function get_chat_messages($chat_id, $limit, $offset)
  {
    $chat_messages = DB::table('chat_messages')
                        ->where('chat_id', $chat_id)
                        ->latest()
                        ->offset($offset)
                        ->limit($limit)
                        ->get();
    return $chat_messages;
  }

  public function get_latest_message($chat_id)
  {
    $chat_message = DB::table('chat_messages')
                        ->where('chat_id', $chat_id)
                        ->latest()
                        ->first();
    return $chat_message;
  }

  public function get_chat_distinct_users($chat_id)
  {
    $users = DB::table('chat_messages')
                    ->join('users', 'user_id', '=', 'users.id')
                    ->select('users.id', 'users.name', 'users.email')
                    ->where('chat_id', $chat_id)
                    ->distinct()
                    ->get();
    return $users;
  }

  public function get_total_chat_message_count($chat_id)
  {
    $total_count = DB::table('chat_messages')->where('chat_id', $chat_id)->count();
    return $total_count;
  }

  public function insert_chat_message($insert_array)
  {
    $chat_message_id = DB::table('chat_messages')->insertGetId($insert_array);
    return $chat_message_id;
  }
}
