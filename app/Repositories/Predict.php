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
                'replies.reply',
                DB::raw('MAX(replies.created_at) as created'),
                DB::raw('SUM(reply_word.repeat) as repeat_sum'),
                DB::raw('( ( SUM(reply_word.repeat) * 0.6 ) + ( COUNT(*) * 0.4 ) ) as score')
            )
            ->join('replies', 'replies.id', '=', 'reply_word.reply_id')
            ->whereIn('word_id', $ids)
            ->groupBy('replies.reply')

            ->orderBy('score', 'desc')
            ->orderBy('repeat_sum', 'desc')
            ->orderBy('created', 'desc')
            ->orderBy(DB::raw('RAND()'))

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