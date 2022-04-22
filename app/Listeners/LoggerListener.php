<?php

namespace App\Listeners;

use App\Events\WriteLog;
use App\Model\Repository\LogRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoggerListener implements ShouldQueue
{
    public $queue = 'logger';

    protected $logRepository;

    /**
     * Create the event listener.
     *
     * @param LogRepository $logRepository
     */
    public function __construct(LogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * Handle the event.
     *
     * @param  WriteLog  $event
     * @return void
     */
    public function handle(WriteLog $event)
    {
        $this->logRepository->store($event->getMessage());
    }
}
