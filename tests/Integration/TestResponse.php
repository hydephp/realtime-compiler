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

        ob_start();
        $response = $guzzle->get('http://localhost:8080'.$uri);
        ob_end_clean();

        return new static($test, $response);
    }

    public function __construct(IntegrationTestCase $test, ResponseInterface $response)
    {
        $this->test = $test;
        $this->response = $response;
    }
}
