<?php


namespace App\Tests;


use App\Helper\QuestionsHelper;
use PHPUnit\Framework\TestCase;

class QuestionsHelperTest extends TestCase
{
    public function testToLowerCase()
    {
        $text = 'Hello Everyone!';
        $text = QuestionsHelper::toLowerAndReplaceLetters($text);

        $this->assertEquals($text, 'hello everyone!');
    }

    public function testToLowerCase2()
    {
        $text = 'BIG TEXT!@';
        $text = QuestionsHelper::toLowerAndReplaceLetters($text);

        $this->assertEquals($text, 'big text!@');
    }

    public function testToLowerCase3()
    {
        $text = 'nothing changed';
        $text = QuestionsHelper::toLowerAndReplaceLetters($text);

        $this->assertEquals($text, 'nothing changed');
    }

    public function testRemoveLithuanianLetters()
    {
        $text = 'lietuvą';
        $text = QuestionsHelper::toLowerAndReplaceLetters($text);

        $this->assertEquals($text, 'lietuva');
    }

    public function testRemoveLithuanianLetters2()
    {
        $text = 'saldžią pergalę';
        $text = QuestionsHelper::toLowerAndReplaceLetters($text);

        $this->assertEquals($text, 'saldzia pergale');
    }

    public function testRemoveLithuanianLetters3()
    {
        $text = 'ąčęėįššų';
        $text = QuestionsHelper::toLowerAndReplaceLetters($text);

        $this->assertEquals($text, 'aceeissu');
    }

    public function testLowerCaseAndLithuanianLetter()
    {
        $text = 'ĄČEEĮŠŠŲ';
        $text = QuestionsHelper::toLowerAndReplaceLetters($text);

        $this->assertEquals($text, 'aceeissu');
    }

    public function testLowerCaseAndLithuanianLetter2()
    {
        $text = 'Pergalingąjį žmogų';
        $text = QuestionsHelper::toLowerAndReplaceLetters($text);

        $this->assertEquals($text, 'pergalingaji zmogu');
    }

    public function testLowerCaseAndLithuanianLetter3()
    {
        $text = 'ČČččČČčč';
        $text = QuestionsHelper::toLowerAndReplaceLetters($text);

        $this->assertEquals($text, 'cccccccc');
    }

    public function testCalculateHowMuchTimeAgoSeconds()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(20);

        $this->assertEquals('20 sekundes (-ių)', $result);
    }

    public function testCalculateHowMuchTimeAgoSeconds2()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(30);

        $this->assertEquals('30 sekundes (-ių)', $result);
    }

    public function testCalculateHowMuchTimeAgoSeconds3()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(42);

        $this->assertEquals('42 sekundes (-ių)', $result);
    }

    public function testCalculateHowMuchTimeAgoMinutes()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(60);

        $this->assertEquals('1 minutę (-es)', $result);
    }

    public function testCalculateHowMuchTimeAgoMinutes2()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(180);

        $this->assertEquals('3 minutę (-es)', $result);
    }

    public function testCalculateHowMuchTimeAgoMinutes3()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3000);

        $this->assertEquals('50 minutę (-es)', $result);
    }

    public function testCalculateHowMuchTimeAgoHours()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600);

        $this->assertEquals('1 valandą (-as)', $result);
    }

    public function testCalculateHowMuchTimeAgoHours2()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(7200);

        $this->assertEquals('2 valandą (-as)', $result);
    }

    public function testCalculateHowMuchTimeAgoHours3()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(36024);

        $this->assertEquals('10 valandą (-as)', $result);
    }
    // nuo cia pakeist
    public function testCalculateHowMuchTimeAgoDays()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 5);

        $this->assertEquals('5 dieną (-as)', $result);
    }

    public function testCalculateHowMuchTimeAgoDays2()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 2);

        $this->assertEquals('2 dieną (-as)', $result);
    }

    public function testCalculateHowMuchTimeAgoDays3()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 1);

        $this->assertEquals('1 dieną (-as)', $result);
    }

    public function testCalculateHowMuchTimeAgoWeeks()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 7 * 3);

        $this->assertEquals('3 savaitę (-as)', $result);
    }

    public function testCalculateHowMuchTimeAgoWeeks2()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 7);

        $this->assertEquals('1 savaitę (-as)', $result);
    }

    public function testCalculateHowMuchTimeAgoWeeks3()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 7 * 2);

        $this->assertEquals('2 savaitę (-as)', $result);
    }

    public function testCalculateHowMuchTimeAgoMonths()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 7 * 5);

        $this->assertEquals('1 mėnesį (-ius)', $result);
    }

    public function testCalculateHowMuchTimeAgoMonths2()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 7 * 5 * 2);

        $this->assertEquals('2 mėnesį (-ius)', $result);
    }

    public function testCalculateHowMuchTimeAgoMonths3()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 7 * 5 * 10);

        $this->assertEquals('11 mėnesį (-ius)', $result);
    }

    public function testCalculateHowMuchTimeAgoYears()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 7 * 5 * 13);

        $this->assertEquals('1 metus', $result);
    }

    public function testCalculateHowMuchTimeAgoYears2()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 7 * 5 * 13 * 3);

        $this->assertEquals('3 metus', $result);
    }

    public function testCalculateHowMuchTimeAgoYears3()
    {
        $result = QuestionsHelper::calculateHowMuchTimeAgo(3600 * 24 * 7 * 5 * 13 * 5);

        $this->assertEquals('6 metus', $result);
    }
}