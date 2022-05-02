<?php

namespace App\Providers;

use App\Infrastructure\Notification\Events\WriteLog;
use App\Infrastructure\Notification\Listeners\LoggerListener;
use App\Infrastructure\Notification\Listeners\LoginSuccessfulListener;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        WriteLog::class => [
            LoggerListener::class,
        ],
        Login::class => [
            LoginSuccessfulListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
