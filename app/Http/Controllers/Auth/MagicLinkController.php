<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserMagicToken;

class MagicLinkController extends Controller
{
    public function send(Request $request) {
        $this->validate($request, [
          'email'    => 'required|email|max:255|exists:users,email',
          'remember' => 'string'
        ]);
        $user = User::getByEmail($request->email);
        
        UserMagicToken::where('user_id',$user->id)->delete();
        $magic_token = UserMagicToken::create([
          'user_id' => $user->id,
          'token'   =>  bin2hex(openssl_random_pseudo_bytes(32))
        ]);
        
        $remember_me = $request->input('remember','off') == 'on' ? 1 : 0;
        $mail_sent   = $magic_token->sendMail($remember_me);
        
        if ($mail_sent) {
            return response()->json([
                'error' => 'Error while sending email'
            ],500);
        }
        
        return response()->json([
            'message' => 'Mail sent successfully'  
        ]);
    }
}
