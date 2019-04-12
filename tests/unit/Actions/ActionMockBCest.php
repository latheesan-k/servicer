<?php

use MVF\Servicer\Actions\ActionMockB;
use Symfony\Component\Console\Output\ConsoleOutput;

class ActionMockBCest
{
    public function classBuilderShouldReturnsUndefinedActionObject(UnitTester $I)
    {
        $console = $I->make(ConsoleOutput::class);
        $mock = new ActionMockB($console);
        $mock->handle([], []);
    }
}
