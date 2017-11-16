<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UseLoginToken extends Model
{
    protected $fillable = ['user_id', 'token', 'user_agent'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
