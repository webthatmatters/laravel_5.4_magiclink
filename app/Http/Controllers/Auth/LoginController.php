<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Carbon\Carbon;
use JWTAuth;

use App\Models\User;
use App\Models\UserMagicToken;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
  /*
  |--------------------------------------------------------------------------
  | Login Controller
  |--------------------------------------------------------------------------
  |
  | This controller handles authenticating users for the application and
  | redirecting them to your home screen. The controller uses a trait
  | to conveniently provide its functionality to your applications.
  |
  */

  use AuthenticatesUsers;

  /**
   * Where to redirect users after login.
   *
   * @var string
   */
  protected $redirectTo = '/home';

  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('guest')->except('logout');
  }
  
  /**
   * Validate the user login request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return void
   */
  protected function validateLogin(Request $request)
  {
    $this->validate($request, [
      $this->username() => 'required|string',
    ]);
  }
  
  /**
   * Attempt to log the user into the application.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return bool
   */
  protected function attemptLogin(Request $request)
  {
    $token = UserMagicToken::where('token',$request->token)->first();
    $user  = User::where('email',$request->email)->first();
    
    return $token != null 
      && $user != null
      && $user->magic_token != null
      && $user->magic_token->token === $request->token
      && !$token->isExpired();
  }
  
  /**
   * Send the response after the user was authenticated.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  protected function sendLoginResponse(Request $request)
  {
      $this->clearLoginAttempts($request);
      $token = UserMagicToken::where('token',$request->token)->first();
      $user  = User::where('email',$request->email)->first();
      
      $jwt = JWTAuth::fromUser($user);
      $token->delete();
      return response()->json([
        'token' => $jwt  
      ]);
  }
}
