<?php


namespace App\Tests\SmokeTests;


use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class ForgetPasswordControllerTest extends TestCase
{
    private Client $client;

    public function setUp(): void
    {
        $this->client = new Client([
            'base_uri' => $_ENV['LOCAL_WEB_DEV_IP'],
            'http_errors' => false
        ]);
    }

    public function testForgetPassword(): void
    {
        $response = $this->client->request('GET', '/forget/password');

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testResetPassword(): void
    {
        $response = $this->client->request('GET', '/reset/password/42424224');

        $this->assertEquals(200, $response->getStatusCode());
    }
}
