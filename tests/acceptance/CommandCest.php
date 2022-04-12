<?php

use Symfony\Component\Console\Output\BufferedOutput;

class CommandCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    // tests
    public function createDatabaseTest(AcceptanceTester $I)
    {
        $output = new BufferedOutput();
        $I->callArtisan('db:create', [], $output);
        $I->assertRegexp('/Database created!/', $output->fetch());
    }

    public function rabbitStartTest(AcceptanceTester $I)
    {
        $output = new BufferedOutput();
        $I->callArtisan('rabbit:receive --exchange=v1.testing -o', [], $output);
        $I->assertRegexp('/Rabbit-receiver stopped!/', $output->fetch());
    }

    public function rabbitStopTest(AcceptanceTester $I)
    {
        $output = new BufferedOutput();
        $I->callArtisan('rabbit:receive:stop', [], $output);
        $I->assertRegexp('/Rabbit-receiver stopped!/', $output->fetch());
    }
}
