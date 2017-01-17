<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

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

        $chat_messages = DB::table('chat_messages')
                                  ->where('chat_id', $chat_id)
                                  ->where('user_id', $user['id'])
                                  ->latest()
                                  ->get();

        return response()->json(['data' => $chat_messages], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
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

        $user = json_decode($user_json);
        $message = $request->input('message');

        $insert_array = array(
          'user_id' => $user['id'],
          'chat_id' => $chat_id,
          'message' => $message,
          'created_at' => gmdate('c', time())
        );
        $chat_message_id = DB::table('chat_messages')->insertGetId($insert_array);

        return response()->json(['data' => $chat_message_id], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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
