<?php

namespace MVF\Servicer\Tests;

use AspectMock\Test;
use Codeception\Stub\Expected;
use MVF\Servicer\Actions\ActionMockA;
use MVF\Servicer\Actions\ActionMockB;
use MVF\Servicer\Actions\ClassBuilder;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\EventHandler;
use MVF\Servicer\UndefinedAction;
use Symfony\Component\Console\Output\ConsoleOutput;

class ConstantsTest extends \Codeception\Test\Unit
{
    public function testShouldReturnValueIfConstantIsDefined()
    {
        self::assertEquals(ActionMockA::class, Constant::getAction(EventHandler::class . '::MOCK'));
    }

    public function testShouldReturnUndefinedActionIfConstantIsNotDefined()
    {
        self::assertEquals(UndefinedAction::class, Constant::getAction(EventHandler::class . '::TEST'));
    }
}
