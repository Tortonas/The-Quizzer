<?php


namespace App\Tests\SmokeTests;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class NonExistantEndpointsTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $_ENV['LOCAL_WEB_DEV_IP'],
            'http_errors' => false
        ]);
    }

    public function testNonExistant1(): void
    {
        $response = $this->client->request('GET', '/wwewqewqeqw');

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testNonExistant2(): void
    {
        $response = $this->client->request('GET', '/55554241');

        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testNonExistant3(): void
    {
        $response = $this->client->request('GET', '/w5e5w4ew7e4w');

        $this->assertEquals(404, $response->getStatusCode());
    }
}