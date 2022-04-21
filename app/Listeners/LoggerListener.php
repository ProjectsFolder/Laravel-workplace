<?php

namespace App\Listeners;

use App\Events\UserLogin;
use App\Mail\UserDetails;
use App\Model\Repository\LogRepository;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailer;

class LoggerListener implements ShouldQueue
{
    public $queue = 'login';

    protected $logRepository;
    protected $mailer;

    /**
     * Create the event listener.
     *
     * @param LogRepository $logRepository
     * @param Mailer $mailer
     */
    public function __construct(LogRepository $logRepository, Mailer $mailer)
    {
        $this->logRepository = $logRepository;
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  UserLogin  $event
     * @return void
     */
    public function handle(UserLogin $event)
    {
        $user = $event->getUser();
        $this->logRepository->store("user {$user->name} is login");
        if (!empty($user->email)) {
            $this->mailer->to($user)->send(new UserDetails($user));
        }
    }
}
