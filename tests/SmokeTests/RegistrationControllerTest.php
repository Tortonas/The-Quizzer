<?php


namespace App\Tests\SmokeTests;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class RegistrationControllerTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $_ENV['LOCAL_WEB_DEV_IP'],
            'http_errors' => false
        ]);
    }

    public function testRegisterEndpoint(): void
    {
        $response = $this->client->request('GET', '/register');

        $this->assertEquals(200, $response->getStatusCode());
    }
}
