<?php

namespace App\External\Interfaces;

use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public function request(string $method, $uri = '', array $options = []): ResponseInterface;
}
