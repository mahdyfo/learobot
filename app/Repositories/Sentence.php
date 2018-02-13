<?php

namespace App\Repositories;

class Sentence
{
    /**
     * Extract the words from a string
     *
     * @param string $text Contains words separated by space
     * @return array Array of the Words extracted from text
     */
    public function getWords($text)
    {
        $text = str_replace(['+', '-', '*', '%', '"', ')', '(', '<', '>', '~'], ' ', $text);
        $text = str_replace('?', '\?', $text);
        $text = preg_replace('/(\r?\n)/', ' ', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        //$text = preg_replace('/\n/s', ' ', $text);
        $words = explode(' ', trim($text));

        if ($words) {
            //unique
            $words = array_unique($words);

            //not empty
            $words = array_filter($words, function ($var) {
                return !empty($var) && mb_strlen($var) > 2 && !preg_match('/\@/', $var);
            });

            //trim
            $words = array_map('trim', $words);
        } else {
            $words = [trim($text)];
        }

        return $words;
    }

    public function censorWords($words_array)
    {

    }
}