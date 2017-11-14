<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    public function words()
    {
        return $this->belongsToMany('App\Word');
    }
}
