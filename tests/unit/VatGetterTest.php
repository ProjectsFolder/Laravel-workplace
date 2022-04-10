<?php

use App\Domain\Interfaces\Output\VatGetterInterface;
use Codeception\Test\Unit;

class VatGetterTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    /**
     * @var VatGetterInterface
     */
    protected $getter;

    protected function _before()
    {
        $this->getter = resolve(VatGetterInterface::class);
    }

    protected function _after()
    {
    }

    public function testFailures()
    {
        $result = $this->getter->get('123456');
        $this->assertEmpty($result);

        $result = $this->getter->get('RU0000000');
        $this->assertEmpty($result);
    }

    public function testSuccess()
    {
        $result = $this->getter->get('LV00000');
        $this->assertNotEmpty($result);
        if (!empty($result)) {
            $this->assertFalse($result->getValid());
        }

        $result = $this->getter->get('LV44103001941');
        $this->assertNotEmpty($result);
        if (!empty($result)) {
            $this->assertTrue($result->getValid());
        }
    }
}
