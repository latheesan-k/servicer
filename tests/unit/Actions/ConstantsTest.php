<?php

namespace MVF\Servicer\Action\Tests;

use MVF\Servicer\Actions\ActionMockA;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\EventHandler;
use MVF\Servicer\UndefinedAction;

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
