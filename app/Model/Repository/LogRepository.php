<?php

namespace App\Model\Repository;

use App\Model\Entity\Log;

class LogRepository
{
    public function store(string $message): int
    {
        $log = new Log();
        $log->message = $message;
        $log->save();

        return $log->id;
    }
}
