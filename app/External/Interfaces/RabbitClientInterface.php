<?php

namespace App\External\Interfaces;

interface RabbitClientInterface
{
    public function send(string $exchangeName, string $message);
    public function createConsumer(string $exchangeName, string $queueName);
    public function reconnect();
}
