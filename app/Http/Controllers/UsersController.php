<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Validator;
use App\Users;

class UsersController extends Controller
{
    public function index(Request $request)
    {
      //
    }

    /**
     * Create a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $validator = Validator::make($request->all(),
          ['name' => 'required'],
          ['email' => 'required'],
          ['password' => 'required|confirmed'],
          ['password_confirmation' => 'required']
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

      // post vars
      $name = $request->input('name');
      $email = $request->input('email');
      $password = $request->input('password');

      // insert new user
      $users_model = new Users();
      $insert_array = array(
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password)
      );
      $user_id = $users_model->insert_user($insert_array);

      // build response
      $data = array('id' => $user_id, 'name' => $name, 'email' => $email);
      $meta = (object)array();
      return response()->json(['data' => $data, 'meta' => $meta], 201);
    }

    // GET /users/current
    public function show()
    {
      try {
        // authenticate
        $user_json = JWTAuth::parseToken()->authenticate();

      } catch (JWTException $e) {
        // something went wrong
        return response()->json(['error' => $e], 500);
      }

      $user = json_decode($user_json, true);
      $data = array('id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']);
      return response()->json(['data' => $data, 'meta' => (object)array()], 200);
    }

    // PATCH /users/current
    public function update(Request $request)
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
          ['email' => 'required'],
          ['password' => 'required|confirmed'],
          ['password_confirmation' => 'required']
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

      $name = $request->input('name');
      $email = $request->input('email');
      $password = $request->input('password');

      // Update the user info
      $users_model = new Users();
      $update_array = array(
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password)
      );
      $users_model->update_user($user['id'], $update_array);

      $data = array('id' => $user['id'], 'name' => $name, 'email' => $email);
      return response()->json(['data' => $data, 'meta' => (object)array()], 200);
    }
}
