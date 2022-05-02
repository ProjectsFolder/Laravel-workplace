<?php

use App\Infrastructure\Notification\Jobs\WriteLog;
use App\Model\Repository\LogRepository;
use Codeception\Test\Unit;

class JobTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testWriteLog()
    {
        $job = new WriteLog('test');
        $job->handle($this->tester->getApplication()->get(LogRepository::class));
    }
}
