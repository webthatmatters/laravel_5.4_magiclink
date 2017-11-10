<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;
use App\User;
use App\Mail\MagicLink;
use Log;
class UserMagicToken extends Model
{
    public static $expirationInMinutes = 5;
    protected $fillable = ['user_id', 'token'];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function sendMail($remember_me) {
        $url = route('magic_token_login',[
            'token'     => $this->token,
            'remember'  => $remember_me,
            'email'     => $this->user->email,
        ]);
        
        try {
            Mail::to($this->user->email)->send(new MagicLink($url));
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    
    public function isExpired() {
        return $this->created_at->diffInMinutes(Carbon::now()) > self::$expirationInMinutes;
    }

    public function belongsToUser($email) {
        $user = User::getByEmail($email);
        
        if(!$user || $user->magic_token == null) {
            return false;
        }
        
        return ($this->token === $user->magic_token->token);
    }
}
