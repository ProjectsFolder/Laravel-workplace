<?php

use App\Domain\Entity\Vat\VatData;
use App\Domain\Interfaces\Output\VatGetterInterface;
use App\Domain\Interfaces\Output\VatSaverInterface;
use App\Domain\VatSaver;
use Codeception\Test\Unit;

class VatSaverTest extends Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;
    protected $vatSaver;

    protected function _before()
    {
        $this->vatSaver = $this->makeEmpty(VatSaverInterface::class, [
            'store' => function () {
                return 1;
            },
        ]);
    }

    protected function _after()
    {
    }

    // tests
    public function testSuccessfulSave()
    {
        $vatGetter = $this->makeEmpty(VatGetterInterface::class, [
            'get' => function () {
                $data = new VatData();
                $data->setValid(true);

                return $data;
            },
        ]);

        $saver = new VatSaver($vatGetter, $this->vatSaver);
        $id = $saver->saveVat('SomeCode');
        $this->assertEquals(1, $id);
    }

    public function testNotSuccessfulSave()
    {
        $vatGetter = $this->createMock(VatGetterInterface::class);
        $vatGetter->method('get')->with('RU123456')->will($this->returnCallback(function () {
            $data = new VatData();
            $data->setValid(false);

            return $data;
        }));
        $saver = new VatSaver($vatGetter, $this->vatSaver);
        $id = $saver->saveVat('RU123456');
        $this->assertEmpty($id);

        $this->expectExceptionMessage('get exception');
        $vatGetter->method('get')->with('RU123456')->will($this->returnCallback(function () {
            throw new Exception('get exception');
        }));
        $saver = new VatSaver($vatGetter, $this->vatSaver);
        $saver->saveVat('RU123456');
    }
}
