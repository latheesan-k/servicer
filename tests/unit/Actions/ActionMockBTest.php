<?php

namespace MVF\Servicer\Tests;

use AspectMock\Test;
use Codeception\Stub\Expected;
use MVF\Servicer\Actions\ActionMockA;
use MVF\Servicer\Actions\ActionMockB;
use MVF\Servicer\Actions\ClassBuilder;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\UndefinedAction;
use Symfony\Component\Console\Output\ConsoleOutput;

class ActionMockBTest extends \Codeception\Test\Unit
{
    public function testClassBuilderReturnsUndefinedActionObject()
    {
        $mock = $this->make(ActionMockB::class);
        $mock->handle((object)[], (object)[]);
    }
}
