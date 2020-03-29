<?php


namespace App\Helper;


class QuestionsHelper
{
    public static function calculateHowMuchTimeAgo($since)
    {
        $count = -1;
        $name = null;
        $chunks = array(
            array(60 * 60 * 24 * 365 , 'metus'),
            array(60 * 60 * 24 * 30 , 'mėnesį (-ius)'),
            array(60 * 60 * 24 * 7, 'savaitę (-as)'),
            array(60 * 60 * 24 , 'dieną (-as)'),
            array(60 * 60 , 'valandą (-as)'),
            array(60 , 'minutę (-es)'),
            array(1 , 'sekundes (-ių)')
        );

        for ($i = 0, $j = count($chunks); $i < $j; $i++) {
            $seconds = $chunks[$i][0];
            $name = $chunks[$i][1];
            if (($count = floor($since / $seconds)) != 0) {
                break;
            }
        }

        $print = ($count == 1) ? '1 '.$name : "$count {$name}";

        return $print;
    }

    public static function toLowerAndReplaceLetters($text)
    {
        $lithuanianLetters = array('ą', 'č', 'ę', 'ė', 'į', 'š', 'ų', 'ū', 'ž', 'Ą', 'Č', 'Ę', 'Ė', 'Į', 'Š', 'Ų', 'Ū', 'Ž');
        $latinLetters = array('a', 'c', 'e', 'e', 'i', 's', 'u', 'u', 'z', 'a', 'c', 'e', 'e', 'i', 's', 'u', 'u', 'z');


        $returnText = str_replace($lithuanianLetters, $latinLetters, $text);

        $returnText = strtolower($returnText);


        return $returnText;
    }
}