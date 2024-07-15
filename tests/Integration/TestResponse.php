<?php

namespace Hyde\RealtimeCompiler\Tests\Integration;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class TestResponse
{
    protected ResponseInterface $response;
    protected IntegrationTestCase $test;

    public static function get(IntegrationTestCase $test, string $uri): static
    {
        $guzzle = new Client();
        $response = $guzzle->get('http://localhost:8080'.$uri);

        return new static($test, $response);
    }

    public function __construct(IntegrationTestCase $test, ResponseInterface $response)
    {
        $this->test = $test;
        $this->response = $response;
    }

    public function assertStatus(int $code): static
    {
        $this->test->assertSame($code, $this->response->getStatusCode());

        return $this;
    }
}
