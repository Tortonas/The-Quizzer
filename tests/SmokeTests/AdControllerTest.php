<?php


namespace App\Tests\SmokeTests;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class AdControllerTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client(['base_uri' => $_ENV['LOCAL_WEB_DEV_IP']]);
    }

    public function testAdEndpoint()
    {
        $response = $this->client->request('GET', '/ad');

        $this->assertEquals(200, $response->getStatusCode());
    }
}
