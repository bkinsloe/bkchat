<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class ChatsController extends Controller
{

  public function __construct()
  {
    // try {
    //   // authenticate
    //   $user_json = JWTAuth::parseToken()->authenticate();
    //
    // } catch (JWTException $e) {
    //     // something went wrong
    //     return response()->json(['error' => $e], 500);
    // }
  }

  /**
   * Lists the chats.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    try {
      // authenticate
      $user_json = JWTAuth::parseToken()->authenticate();

    } catch (JWTException $e) {
        // something went wrong
        return response()->json(['error' => $e], 500);
    }

    $page = $request->input('page');
    $limit = $request->input('limit');

    //return json_encode(array('page' => $page, 'limit' => $limit));
    $user = json_decode($user_json, true);

    $total_count = DB::table('chats')->where('user_id', $user['id'])->count();
    $chats = DB::table('chats')->where('user_id', $user['id'])->get();
    return response()->json(['data' => $total_count], 200);
  }

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
      //
      return 'create chat';
  }

  /**
   * Creates a new chat
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    try {
      // authenticate
      $user_json = JWTAuth::parseToken()->authenticate();

    } catch (JWTException $e) {
        // something went wrong
        return response()->json(['error' => $e], 500);
    }

    $name = $request->input('name');
    $message = $request->input('message');

    $user = json_decode($user_json, true);

    $insert_array = array(
      'user_id' => $user['id'],
      'name' => $name,
      'created_at' => gmdate('c', time())
    );
    $chat_id = DB::table('chats')->insertGetId($insert_array);

    $insert_array = array(
      'user_id' => $user['id'],
      'chat_id' => $chat_id,
      'message' => $message,
      'created_at' => gmdate('c', time())
    );
    $chat_message_id = DB::table('chat_messages')->insertGetId($insert_array);

    return response()->json(['data' => $chat_id], 201);
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $chat = DB::table('chats')->where('id', $id)->first();
    return $chat;
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function edit($id)
  {
      //
  }

  /**
   * Update the chat name by id.
   *
   * PATCH
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    try {
      // authenticate
      $user_json = JWTAuth::parseToken()->authenticate();

    } catch (JWTException $e) {
      // something went wrong
      return response()->json(['error' => $e], 500);
    }

    $name = $request->input('name');
    $user = json_decode($user_json);

    // update chat name
    DB::table('chats')
          ->where('id', $id)
          ->where('user_id', $user['id'])
          ->update(array('name' => $name));

    // get updated chat
    $chat = DB::table('chats')
                  ->where('id', $id)
                  ->where('user_id', $user['id'])
                  ->first();

    return response()->json(['data' => $chat], 200);
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
      //
  }
}
