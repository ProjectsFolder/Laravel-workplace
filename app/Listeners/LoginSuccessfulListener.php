<?php

namespace App\Listeners;

use App\Events\WriteLog;
use App\Mail\UserDetails;
use App\Model\Entity\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailer;

class LoginSuccessfulListener implements ShouldQueue
{
    public $queue = 'login';

    protected $dispatcher;
    protected $mailer;

    /**
     * Create the event listener.
     *
     * @param Dispatcher $dispatcher
     * @param Mailer $mailer
     */
    public function __construct(Dispatcher $dispatcher, Mailer $mailer)
    {
        $this->dispatcher = $dispatcher;
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        /** @var User $user */
        $user = $event->user;
        $this->dispatcher->dispatch(new WriteLog("user {$user->name} is login"));
        if (!empty($user->email)) {
            $this->mailer->to($user)->send(new UserDetails($user));
        }
    }
}
