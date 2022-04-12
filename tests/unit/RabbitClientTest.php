<?php

use App\External\Interfaces\RabbitClientInterface;
use Codeception\Test\Unit;

class RabbitClientTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    /**
     * @var RabbitClientInterface
     */
    protected $client;

    protected function _before()
    {
        $this->client = $this->tester->getApplication()->get(RabbitClientInterface::class);
    }

    protected function _after()
    {
    }

    public function test()
    {
        $this->client->send('v1.testing', 'Test Message');
        $consumer = $this->client->createConsumer('v1.testing', 'test');
        $this->assertNotEmpty($consumer);
    }
}
