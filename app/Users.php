<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Users extends Model
{
    public function get_user($email)
    {
        $user = DB::table('users')
                  ->where('email', $email)
                  ->first();
        return $user;
    }

    public function insert_user($insert_array)
    {
      $user_id = DB::table('users')->insertGetId($insert_array);
      return $user_id;
    }

    public function update_user($user_id, $update_array)
    {
      DB::table('users')->where('id', $user_id)->update($update_array);
    }
}
