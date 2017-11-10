<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use Auth;
use Carbon\Carbon;
use Log;

use App\User;
use App\UserMagicToken;
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
  
  public function sendMagicLink(Request $request) {
    $this->validate($request, [
      'email'    => 'required|email|max:255|exists:users,email',
      'remember' => 'string'
    ]);
    $user = User::getByEmail($request->email);
    
    if (!$user) 
      return redirect('register')->with('error', 'User not found. Please check your email again.');
    
    UserMagicToken::where('user_id',$user->id)->delete();
    $magic_token = UserMagicToken::create([
      'user_id' => $user->id,
      'token'   => str_random(50)
    ]);
    
    $remember_me = $request->input('remember','off') == 'on' ? 1 : 0;
    $mail_sent   = $magic_token->sendMail($remember_me);
    if($mail_sent)
      return back()->with('success', 'We have sent you a magic link! The link expires in 5 minutes');
    else
      return back()->with('error', 'Something went wrong. Please try again.');
  }
  
  public function authenticate(Request $request, $token) {
    $token = UserMagicToken::where('token',$token)->first();
    
    if (!$token || !$token->belongsToUser($request->email))
      return redirect('/login')->with('error', 'Invalid magic link.');
    
    if ($token->isExpired()) {
      $token->delete();
      return redirect('/login')->with('error', 'That magic link has expired.');
    }
    
    Auth::login($token->user, $request->remember);
    $token->delete();
    return redirect('home');
  }
}
