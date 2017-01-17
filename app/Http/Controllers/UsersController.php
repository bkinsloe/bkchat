<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class UsersController extends Controller
{
    public function index(Request $request)
    {
      exit;

      $insert_array = array(
        'name' => $request->input('name'),
        'email' => $request->input('email'),
        'password' => Hash::make($request->input('password'))
      );
      $user_id = DB::table('users')->insertGetId($insert_array);

      $message = '';
      $errors = array();
      $data = array();
      $meta = array();

      $response = array('data' => $data, 'meta' => $meta);

      //

      return response()->json($response, 200);
      return json_encode($response);
    }

    /**
     * Create a new user.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
      $this->validate($request, [
        'name' => 'required',
        'email' => 'required',
        'password' => 'required',
        'password_confirmation' => 'required'
      ]);

      // post vars
      $name = $request->input('name');
      $email = $request->input('email');
      $password = $request->input('password');
      $password_confirm = $request->input('password_confirmation');

      // Check password and confirm passwords match
      if ($password === $password_confirm)
      {
        $insert_array = array(
          'name' => $name,
          'email' => $email,
          'password' => Hash::make($password)
        );
        $user_id = DB::table('users')->insertGetId($insert_array);

        $data = array('id' => $user_id, 'name' => $name, 'email' => $email);
        $meta = array();
        return response()->json(['data' => $data, 'meta' => $meta], 201);
      } else {
        return response()->json(['data' => $data, 'meta' => $meta], 201);
      }

      $message = '';
      $errors = array();
      $data = array('id' => $user_id, 'name' => $name, 'email' => $email);
      $meta = array();

      return response()->json(['data' => $data, 'meta' => $meta], 201);
    }

    public function current()
    {
      // try {
      //   // authenticate
      //   $user_json = JWTAuth::parseToken()->authenticate();
      //
      // } catch (JWTException $e) {
      //   // something went wrong
      //   return response()->json(['error' => $e], 500);
      // }
      //
      // return response()->json(['data' => $user_json], 200);
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

      return response()->json(['data' => $user_json], 200);
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

      $user = json_decode($user_json, true);

      $name = $request->input('name');
      $email = $request->input('email');
      $password = $request->input('password');
      $confirm_password = $request->input('password_confirmation');

      $update_array = array(
        'name' => $name,
        'email' => $email,
        'password' => Hash::make($password)
      );
      DB::table('users')->where('id' => $user['id'])->update($update_array);

      return response()->json(['data' => $user_json], 200);
    }
}
