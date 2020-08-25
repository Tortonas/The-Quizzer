<?php


namespace App\Tests\SmokeTests\Integration;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class GoogleLoginControllerTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $_ENV['LOCAL_WEB_DEV_IP'],
            'http_errors' => false
        ]);
    }

    public function testLoginGoogle(): void
    {
        $response = $this->client->request('GET', '/login/google');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testLoginGoogleCheck(): void
    {
        $response = $this->client->request('GET', '/login/google/check');

        $this->assertEquals(200, $response->getStatusCode());
    }
}
