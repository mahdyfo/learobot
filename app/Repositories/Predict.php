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

    public function getMostRelativeReply($min_score = false)
    {
        if (!$min_score) {
            $min_score = config('settings.min_score');
        }

        $sentence = new Sentence;
        $words = $sentence->getWords($this->text);
        $words = $this->formatBooleanSearch($words);

        $word_model = new Word;
        $ids = $word_model->matchWord($words)->get();

        $result = DB::table('reply_word')
            ->select(
                'reply',
                DB::raw('((SUM(repeat) * .7) + (COUNT(*) * .3)) as score')
            )
            ->join('replies', 'replies.id', '=', 'reply_word.reply_id')
            ->whereIn('word_id', $ids)
            ->groupBy('reply_word.reply_id')
            ->orderBy('effect', 'desc')
            ->first();

        if ($result && $result->score >= $min_score) {
            return $result->reply;
        }

        return null;
    }

    private function formatBooleanSearch($input_array)
    {
        $text = implode(' ', $input_array);

        return $text;
    }
}