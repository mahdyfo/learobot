<?php

namespace App\Repositories;

use App\Reply;
use App\Word;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class Train
{
    /**
     * Extract the words from a string
     *
     * @param string $text Contains words separated by space
     * @return array Array of the Words extracted from text
     */
    public function getWords($text)
    {
        $words = explode(' ', $text);

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

    /**
     * Find ids of an array of words from database
     *
     * @param array $words
     * @return array Ids of words in db
     */
    public function findWords($words)
    {
        $word = new Word;
        return $word->select('id')->whereIn('word', $words)->get()->pluck('id');
    }

    /**
     * Save Reply text in database
     *
     * @param string $text reply text
     * @return Reply Object of the inserted reply
     */
    public function saveReply($text)
    {
        $reply = new Reply;

        try {
            $reply->reply = $text;
            $reply->save();
            return $reply;
        } catch (QueryException $e) {
            return $reply->where('reply', 'like', '%' . $text . '%')->first();
        }
    }

    /**
     * Save Words of a text in database
     *
     * @param string $text contains words separated by space
     * @return array ids of the given words from db
     */
    public function saveWords($text)
    {
        //explode words
        $words = $this->getWords($text);

        //format sql insert
        $query = [];
        foreach ($words as $word) {
            $query[] = '(?)';
        }

        //insert unique words
        DB::insert(DB::raw('INSERT IGNORE INTO words(`word`) VALUES ' . implode(',', $query)), $words);

        //return words ids
        return $this->findWords($words);
    }
}