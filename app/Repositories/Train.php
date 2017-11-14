<?php

namespace App\Repositories;

use App\Reply;
use Illuminate\Database\QueryException;

class Train
{
    public function getWords($input)
    {
        $words = explode(' ', $input);

        //unique
        $words = array_unique($words);

        //not empty
        $words = array_filter($words, function ($var) {
            return !empty($var);
        });

        //trim
        $words = array_map('trim', $words);

        return $words;
    }

    public function saveReply($text)
    {
        $reply = new Reply();

        try {
            $reply->reply = $text;
            $reply->save();
            return $reply;
        } catch (QueryException $e) {
            return $reply->where('reply', 'like', '%' . $text . '%')->first();
        }
    }
}