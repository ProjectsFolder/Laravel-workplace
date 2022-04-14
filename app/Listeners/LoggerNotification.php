<?php

namespace App\Listeners;

use App\Events\UserLogin;
use App\Model\Repository\LogRepository;
use Illuminate\Contracts\Queue\ShouldQueue;

class LoggerNotification implements ShouldQueue
{
    public $queue = 'login';

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
     * @param  UserLogin  $event
     * @return void
     */
    public function handle(UserLogin $event)
    {
        $this->logRepository->store($event->getMessage());
    }
}
