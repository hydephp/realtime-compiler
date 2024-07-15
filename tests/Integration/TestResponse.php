<?php

namespace Hyde\RealtimeCompiler\Tests\Integration;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

class TestResponse
{
    protected ResponseInterface $response;

    public static function get(string $uri): static
    {
        $guzzle = new Client();
        $response = $guzzle->get('http://localhost:8080'.$uri);

        return new static($response);
    }

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }
}
