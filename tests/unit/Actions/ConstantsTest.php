<?php

namespace MVF\Servicer\Action\Tests;

use MVF\Servicer\Actions\ActionMockA;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\Handlers;
use MVF\Servicer\UndefinedAction;

class ConstantsTest extends \Codeception\Test\Unit
{
    public function testShouldReturnValueIfConstantIsDefined()
    {
        self::assertEquals(ActionMockA::class, Constant::getAction(Handlers::class . '::MOCK'));
    }

    public function testShouldReturnUndefinedActionIfConstantIsNotDefined()
    {
        self::assertEquals(UndefinedAction::class, Constant::getAction(Handlers::class . '::TEST'));
    }
}
