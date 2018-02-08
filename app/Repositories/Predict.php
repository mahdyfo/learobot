<?php

namespace App\Repositories;

use App\Message;
use App\Word;
use Illuminate\Support\Facades\DB;

class Predict
{
    public $text;

    public function __construct(Message $message)
    {
        $this->text = $message->text;
    }

    public function getMostRelativeReply($min_reply_repeat = null)
    {
        $sentence = new Sentence;
        $words = $sentence->getWords($this->text);
        $words = $this->formatBooleanSearch($words);

        $word_model = new Word;
        $ids = $word_model->matchWord($words)->get();

        $result = DB::table('reply_word')
            ->select('reply_id', 'repeat')
            ->whereIn('word_id', $ids)
            ->orderBy('repeat', 'desc')
            ->first();

        if ($result->repeat >= ($min_reply_repeat ? $min_reply_repeat : config('settings.min_repeat'))) {
            return $result;
        }

        return null;
    }

    private function formatBooleanSearch($input_array)
    {
        $text = str_replace($input_array, ['+', '-', '*', '%', '"', ')', '(', '<', '>', '~'], '');
        $text = implode(' ', $text);
        $text = trim($text);

        return $text;
    }
}