<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use App\Chats;
use App\ChatMessages;

class ChatsController extends Controller
{

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

    // calculate the offset
    $offset = $page * $limit - $limit;

    $user = json_decode($user_json, true);

    $chat_messages_model = new ChatMessages();
    $chats_model = new Chats();

    // Get user chats
    $chats = $chats_model->get_user_chats($user['id'], $limit, $offset);
    $data = array();

    // loop through each chat to get user and last message data
    foreach($chats as $chat){
      $chat_array = array('id' => $chat->id, 'name' => $chat->name, 'user_id' => $chat->user_id);
      $chat_array['users'] = json_decode(json_encode($chat_messages_model->get_chat_distinct_users($chat->id)), true);

      $chat_array['last_chat_message'] = $chat_messages_model->get_latest_message($chat->id);
      $last_message_user_id = $chat_array['last_chat_message']->user_id;
      $user_key = array_search($last_message_user_id, array_column($chat_array['users'], 'id'));
      $chat_array['last_chat_message']->{'user'} = $chat_array['users'][$user_key];
      $data[] = $chat_array;
    }

    // get pagination data
    $total_count = $chats_model->get_total_user_chat_count($user['id']);
    $page_count = ceil($total_count / $limit);
    $pagination = array('current_page' => $page, 'per_page' => $limit, 'page_count' => $page_count, 'total_count' => $total_count);

    return response()->json(['data' => $data, 'meta' => array('pagination' => $pagination)], 200);
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

    $validator = Validator::make($request->all(),
        ['name' => 'required'],
        ['message' => 'required']
    );

    if ($validator->fails())
    {
      $message = 'Validation Failed';
      $response = array(
        'message' => $message,
        'errors' => $validator->messages(),
        'meta' => (object)array()
      );
      return response()->json($response, 200);
    }

    $name = $request->input('name');
    $message = $request->input('message');

    $user = json_decode($user_json, true);

    $chats_model = new Chats();
    $chat_messages_model = new ChatMessages();

    // insert the new chat
    $insert_array = array(
      'user_id' => $user['id'],
      'name' => $name,
      'created_at' => gmdate('c', time())
    );
    $chat_id = $chats_model->insert_chat($insert_array);

    // Insert the first chat message for the new chat
    $insert_array = array(
      'user_id' => $user['id'],
      'chat_id' => $chat_id,
      'message' => $message,
      'created_at' => gmdate('c', time())
    );
    $chat_message_id = $chat_messages_model->insert_chat_message($insert_array);

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
    // $chat = DB::table('chats')->where('id', $id)->first();
    // return $chat;
    $chats_model = new Chats();
    $chat = $chats_model->get_chat($id);
    return json_encode($chat);
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
    $chats_model = new Chats();
    $chats_model->update_chat($id, $user['id'], $name);

    // get updated chat
    $chat = $chats_model->get_chat($id, $user['id']);

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
