<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    public function replies()
    {
        return $this->belongsToMany('App\Reply');
    }

    public function scopeMatchWord($query, $text)
    {
        return $query->whereRaw('MATCH word AGAINST (? IN BOOLEAN MODE)', [$text]);
    }
}
