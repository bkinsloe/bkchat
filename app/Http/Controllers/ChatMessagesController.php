<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use App\ChatMessages;

class ChatMessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $chat_id)
    {
        // GET chat messages
        try {
          // authenticate
          $user_json = JWTAuth::parseToken()->authenticate();

        } catch (JWTException $e) {
          // something went wrong
          return response()->json(['error' => $e], 500);
        }

        $user = json_decode($user_json, true);

        $page = $request->input('page');
        $limit = $request->input('limit');

        // calculate the offset
        $offset = $page * $limit - $limit;

        $chat_messages_model = new ChatMessages();
        $chat_messages = $chat_messages_model->get_chat_messages($chat_id, $limit, $offset);
        $chat_users = json_decode(json_encode($chat_messages_model->get_chat_distinct_users($chat_id)), true);
        $data = array();

        // loop through each chat to get user and last message data
        foreach ($chat_messages as $chat_message)
        {
          // format dates
          $chat_message->{'created_at'} = date(DATE_ISO8601, strtotime($chat_message->created_at));
          $chat_message->{'updated_at'} = date(DATE_ISO8601, strtotime($chat_message->updated_at));
          $user_key = array_search($chat_message->user_id, array_column($chat_users, 'id'));
          $chat_message->{'user'} = $chat_users[$user_key];
          $data[] = $chat_message;
        }

        // get pagination data
        $total_count = $chat_messages_model->get_total_chat_message_count($chat_id);
        $page_count = ceil($total_count / $limit);
        $pagination = array('current_page' => $page, 'per_page' => $limit, 'page_count' => $page_count, 'total_count' => $total_count);

        return response()->json(['data' => $data, 'meta' => array('pagination', $pagination)], 200);
    }

    /**
     * Create new chat message.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $chat_id)
    {
        // POST method for creating chat message
        try {
          // authenticate
          $user_json = JWTAuth::parseToken()->authenticate();

        } catch (JWTException $e) {
          // something went wrong
          return response()->json(['error' => $e], 500);
        }

        $validator = Validator::make($request->all(),
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

        $user = json_decode($user_json, true);
        $message = $request->input('message');

        $chat_messages_model = new ChatMessages();
        $insert_array = array(
          'user_id' => $user['id'],
          'chat_id' => $chat_id,
          'message' => $message,
          'created_at' => date('Y-m-d H:i:s')
        );
        $chat_message_id = $chat_messages_model->insert_chat_message($insert_array);

        $data = $insert_array;
        $data['id'] = $chat_message_id;
        $data['created_at'] = date(DATE_ISO8601, strtotime($data['created_at'])); // format to iso8601
        $data['user'] = array('id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']);

        return response()->json(['data' => $data, 'meta' => (object)array()], 201);
    }
}
