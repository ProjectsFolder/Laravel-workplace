<?php

use App\Infrastructure\Notification\Events\WriteLog;
use App\Infrastructure\Notification\Listeners\LoggerListener;
use App\Model\Repository\LogRepository;
use Codeception\Test\Unit;

class EventTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testUserLogin()
    {
        $event = new WriteLog('test');
        $loggerNotification = new LoggerListener($this->tester->getApplication()->get(LogRepository::class));
        $loggerNotification->handle($event);
    }
}
