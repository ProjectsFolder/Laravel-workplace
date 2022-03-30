<?php

namespace App\Model\Repository;

use App\Model\Entity\Log;

class LogRepository
{
    public function create(string $message): Log
    {
        $log = new Log();
        $log->message = $message;
        $log->save();

        return $log;
    }
}
