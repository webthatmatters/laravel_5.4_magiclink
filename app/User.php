<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email',
    ];

    public function magic_token() {
        return $this->hasOne(UserMagicToken::class)->orderBy('created_at','DESC');
    }
    
    protected static function getByEmail($email) {
        return self::where('email', $email)->first();
    }
}
