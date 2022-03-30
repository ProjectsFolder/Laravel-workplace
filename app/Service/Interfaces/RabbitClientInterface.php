<?php

namespace App\Service\Interfaces;

interface RabbitClientInterface
{
    public function send(string $exchangeName, string $message);
    public function createConsumer(string $exchangeName);
}
