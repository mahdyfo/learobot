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
        $words = explode(' ', trim($text));

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
}