<?php

namespace Hyde\RealtimeCompiler\Tests\Integration;

class TestResponse
{
    protected \Psr\Http\Message\ResponseInterface $response;

    public static function get(string $uri): static
    {
        $guzzle = new \GuzzleHttp\Client();
        $response = $guzzle->get('http://localhost:8080'.$uri);

        return new static($response);
    }

    public function __construct(\Psr\Http\Message\ResponseInterface $response)
    {
        $this->response = $response;
    }
}
