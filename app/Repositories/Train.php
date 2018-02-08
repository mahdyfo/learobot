<?php

namespace App\Repositories;

use App\Message;
use App\Reply;
use App\Word;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class Train
{
    public function __construct(Message $message = null)
    {
        $reply = $this->saveReply($message->text);
        $word_ids = $this->saveWords($message->replied_message_text);
        $this->attachWordsToReply($reply, $word_ids);
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
        return $word->select('id')->whereIn('word', $words)->get()->pluck('id')->toArray();
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
        $sentence = new Sentence;

        $words = $sentence->getWords($text);

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

    /**
     * Attach relative words to a reply
     *
     * @param Reply $reply
     * @param array $word_ids
     */
    public function attachWordsToReply(Reply $reply, $word_ids)
    {
        if (count($word_ids) == 0) return;

        foreach ($word_ids as $word_id) {
            if ($word_id <= 0) continue;
            $rows[] = '(' . intval($reply->id) . ', ?)';
        }

        //insert on duplicate key update repeat = repeat+1
        DB::insert(DB::raw(
            'INSERT INTO reply_word(`reply_id`, `word_id`) VALUES '
            . implode(',', $rows)
            . ' ON DUPLICATE KEY UPDATE `repeat`=`repeat`+1'
        ), $word_ids);
    }
}