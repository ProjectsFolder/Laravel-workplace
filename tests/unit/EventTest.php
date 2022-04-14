<?php

use App\Events\UserLogin;
use App\Listeners\LoggerListener;
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
        $event = new UserLogin('test');
        $loggerNotification = new LoggerListener($this->tester->getApplication()->get(LogRepository::class));
        $loggerNotification->handle($event);
    }
}
