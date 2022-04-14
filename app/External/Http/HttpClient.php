<?php

namespace App\External\Http;

use App\External\Interfaces\HttpClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;

/**
 * @codeCoverageIgnore
 */
class HttpClient implements HttpClientInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            RequestOptions::VERIFY => false,
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::TIMEOUT => 10,
        ]);
    }

    public function request(string $method, $uri = '', array $options = []): ResponseInterface
    {
        return $this->client->request($method, $uri, $options);
    }
}
