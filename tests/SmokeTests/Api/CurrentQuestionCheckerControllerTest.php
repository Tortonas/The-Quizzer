<?php


namespace App\Tests\SmokeTests\Api;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class CurrentQuestionCheckerControllerTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $_ENV['LOCAL_WEB_DEV_IP'],
            'http_errors' => false
        ]);
    }

    public function testApiQuestion(): void
    {
        $response = $this->client->request('GET', '/api/question');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testApiReload(): void
    {
        $response = $this->client->request('GET', '/api/reload');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testFrontPageInfo(): void
    {
        $response = $this->client->request('GET', '/api/front_page_info');

        $this->assertEquals(200, $response->getStatusCode());
    }
}
