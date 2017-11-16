<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Mail;

use Carbon\Carbon;
use App\User;
use App\Mail\MagicLink;

class UserMagicToken extends Model
{
    protected $fillable = ['user_id', 'token'];
    
    public function user() {
        return $this->belongsTo(User::class);
    }
    
    public function sendMail($remember_me) {
        $params = http_build_query([
            'token'     => $this->token,
            'remember'  => $remember_me,
            'email'     => $this->user->email,
        ]);
        $url = config('auth.magic_links.url').$params;
        
        try {
            Mail::to($this->user->email)->send(new MagicLink($url));
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    
    public function isExpired() {
        return $this->created_at->diffInMinutes(Carbon::now()) > config('auth.magic_links.expire');
    }

    public function belongsToUser($email) {
        $user = User::getByEmail($email);
        
        if(!$user || $user->magic_token == null) {
            return false;
        }
        
        return ($this->token === $user->magic_token->token);
    }
}
