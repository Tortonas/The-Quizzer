<?php


namespace App\Tests\SmokeTests\Integration;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class FacebookLoginControllerTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $_ENV['LOCAL_WEB_DEV_IP'],
            'http_errors' => false
        ]);
    }

    public function testLoginFacebook(): void
    {
        $response = $this->client->request('GET', '/login/facebook');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testLoginFacebookCheck(): void
    {
        $response = $this->client->request('GET', '/login/facebook/check');

        $this->assertEquals(200, $response->getStatusCode());
    }
}
