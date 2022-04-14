<?php

namespace App\External\Interfaces;

interface SmsClientInterface
{
    public function send(string $phone, string $message);
}
