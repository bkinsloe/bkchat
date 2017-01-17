<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
//use Session;

class AuthController extends Controller
{
  /**
   * login endpoint.
   *
   * @return \Illuminate\Http\Response
   */
  public function login(Request $request)
  {
      // Get the user's email and hashed password inputs
      $email = $request->input('email');
      $password = $request->input('password');

      // Check their credentials in the DB
      $user = DB::table('users')
                ->where('email', $email)
                ->first();

      if (!empty($user))
      {
        // user found
        if (Hash::check($password, $user->password)) {
          $credentials = $request->only('email', 'password');
          // Login successful, set JWT
          try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
          } catch (JWTException $e) {
              // something went wrong whilst attempting to encode the token
              return response()->json(['error' => 'could_not_create_token'], 500);
          }
          // all good so return the token
          return response()->json(compact('token'));
        } else {
          // email found, incorrect password
          return 'password incorrect';
        }
      }
      else
      {
        // no user found
        return 'nope';
      }
  }

  /**
   * logout endpoint.
   *
   * @return \Illuminate\Http\Response
   */
  public function logout()
  {
    JWTAuth::parseToken()->invalidate();
    //$currentUser = JWTAuth::parseToken()->authenticate();

    //return json_encode($currentUser);
    // invalidate the JWT
    //JWTAuth::parseToken()->invalidate();
    //return json_encode($_SESSION);
  }
}