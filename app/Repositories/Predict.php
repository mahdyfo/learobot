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
        $search_ready_words = $this->formatBooleanSearch($words);

        $word_model = new Word;
        $ids = $word_model->matchWord($search_ready_words)->get();

        $results = DB::table('reply_word')
            ->select(
                'replies.reply',
                DB::raw('( ( SUM(reply_word.repeat) * 0.7 ) + ( COUNT(*) * 0.3 ) ) as score'),
                DB::raw('SUM(reply_word.repeat) as repeat_sum'),
                DB::raw('COUNT(*) as countt'),
                DB::raw('MAX(replies.created_at) as created')
            )
            ->join('replies', 'replies.id', '=', 'reply_word.reply_id')
            ->whereIn('word_id', $ids)
            ->groupBy('replies.reply')

            ->orderBy('score', 'desc')
            ->orderBy('repeat_sum', 'desc')
            ->orderBy('countt', 'desc')
            ->orderBy('created', 'desc')
            ->orderBy(DB::raw('RAND()'))
            ->limit(10)
            ->get();

        foreach ($results as $result) {
            //minimum score
            if ($result && $result->score >= $min_score) {
                //has at least x % words of the original replied message or not
                if (($result->countt / count($words)) >= config('settings.min_similarity')) {
                    return $result->reply;
                }
            }
        }

        return null;
    }

    private function formatBooleanSearch($input_array)
    {
        $text = implode(' ', $input_array);

        return $text;
    }
}