<?php

namespace MVF\Servicer\Action\Tests;

use MVF\Servicer\Actions\ActionMockB;
use Symfony\Component\Console\Output\ConsoleOutput;

class ActionMockBTest extends \Codeception\Test\Unit
{
    public function testClassBuilderReturnsUndefinedActionObject()
    {
        $console = $this->make(ConsoleOutput::class);
        $mock = new ActionMockB($console);
        $mock->handle((object)[], (object)[]);
    }
}
