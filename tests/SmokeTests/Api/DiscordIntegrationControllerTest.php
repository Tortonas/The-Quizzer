<?php


namespace App\Tests\SmokeTests\Api;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class DiscordIntegrationControllerTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $_ENV['LOCAL_WEB_DEV_IP'],
            'http_errors' => false
        ]);
    }

    public function testApiDiscordSetname400Get(): void
    {
        $response = $this->client->request('GET', '/api/discord/set_name');

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testApiDiscordSetname400Post(): void
    {
        $response = $this->client->request('POST', '/api/discord/set_name');

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testApiDiscordAnswerQuestionGet(): void
    {
        $response = $this->client->request('GET', '/api/discord/answer_question');

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testApiDiscordAnswerQuestionWithDataPost(): void
    {
        $body = [
            'discord' => '123',
            'answer' => 'randomanswer'
        ];

        $options['body'] = json_encode($body);

        $response = $this->client->request('POST', '/api/discord/answer_question', $options);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testApiDiscordAnswerQuestionWithBadDataPost(): void
    {
        $body = [
            'discordBAD' => '123',
            'answerBAD' => 'randomanswer'
        ];

        $options['body'] = json_encode($body);

        $response = $this->client->request('POST', '/api/discord/answer_question', $options);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testApiDiscordSetNameWithDataPost(): void
    {
        $body = [
            'discord' => '123',
            'name' => 'tester'
        ];

        $options['body'] = json_encode($body);

        $response = $this->client->request('POST', '/api/discord/set_name', $options);

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testApiDiscordSetNameWithBadDataPost(): void
    {
        $body = [
            'discordBAD' => '123',
            'nameBAD' => 'tester'
        ];

        $options['body'] = json_encode($body);

        $response = $this->client->request('POST', '/api/discord/set_name', $options);

        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testApiDiscordCurrentQuestion(): void
    {
        $response = $this->client->request('GET', '/api/discord/current_question');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testApiDiscordSkipQuestion(): void
    {
        $response = $this->client->request('GET', '/api/discord/skip_question');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testApiDiscordPrevQuestionAnswer(): void
    {
        $response = $this->client->request('GET', '/api/discord/prev_question_answer');

        $this->assertEquals(200, $response->getStatusCode());
    }
}
