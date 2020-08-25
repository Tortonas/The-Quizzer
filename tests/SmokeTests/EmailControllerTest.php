<?php


namespace App\Tests\SmokeTests;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class EmailControllerTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $_ENV['LOCAL_WEB_DEV_IP'],
            'http_errors' => false
        ]);
    }

    public function testCancelEmailHash(): void
    {
        $response = $this->client->request('GET', '/cancel/email/5252525');

        $this->assertEquals(200, $response->getStatusCode());
    }
}
