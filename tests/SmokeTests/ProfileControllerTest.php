<?php


namespace App\Tests\SmokeTests;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ProfileControllerTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $_ENV['LOCAL_WEB_DEV_IP'],
            'http_errors' => false
        ]);
    }

    public function testProfileIndex(): void
    {
        $response = $this->client->request('GET', '/profilis');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testProfileChange(): void
    {
        $response = $this->client->request('GET', '/profilis/keisti');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testRandomProfile(): void
    {
        $response = $this->client->request('GET', '/profilis/1');

        $this->assertEquals(200, $response->getStatusCode());
    }
}
