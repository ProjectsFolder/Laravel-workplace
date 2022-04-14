<?php

use App\Exceptions\SmsException;
use App\External\Interfaces\HttpClientInterface;
use App\External\Interfaces\SmsClientInterface;
use Codeception\Test\Unit;
use GuzzleHttp\Psr7\Response;

class SmsClientTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    /**
     * @var SmsClientInterface
     */
    protected $client;

    protected function _before()
    {
        $httpClient = $this->makeEmpty(HttpClientInterface::class, [
            'request' => function () {
                $args = func_get_args();
                $options = $args[2];
                $phone = $options['query']['to'] ?? '';
                if (is_numeric($phone)) {
                    return new Response(200, [], json_encode([
                        'status_code' => 100,
                    ]));
                } elseif ('test1' == $phone) {
                    return new Response(500, [], json_encode([
                        'status_code' => 202,
                    ]));
                } elseif ('test2' == $phone) {
                    return new Response(200, [], '');
                } elseif ('test3' == $phone) {
                    return new Response(200, [], json_encode([
                        'status_code' => 202,
                    ]));
                } else {
                    return new Response(200, [], json_encode([
                        'status_code' => 100,
                        'sms' => [
                            'phone' => [
                                'status_code' => 202,
                            ],
                        ]
                    ]));
                }
            },
        ]);
        $this->tester->getApplication()->instance(HttpClientInterface::class, $httpClient);
        $this->client = $this->tester->getApplication()->get(SmsClientInterface::class);
    }

    protected function _after()
    {
    }

    public function testFailure()
    {
        $this->expectException(SmsException::class);
        $this->client->send('test1', 'Test Message');
    }

    public function testFailure2()
    {
        $this->expectException(SmsException::class);
        $this->client->send('test2', 'Test Message');
    }

    public function testFailure3()
    {
        $this->expectException(SmsException::class);
        $this->client->send('test3', 'Test Message');
    }

    public function testFailure4()
    {
        $this->expectException(SmsException::class);
        $this->client->send('test4', 'Test Message');
    }

    public function testSuccess()
    {
        $this->client->send('79511751999', 'Test Message');
        $this->assertTrue(true);
    }
}
