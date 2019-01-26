<?php

namespace MVF\Servicer\Action\Tests;

use MVF\Servicer\Actions\ActionMockA;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\Events;
use MVF\Servicer\UndefinedEvent;

class ConstantsTest extends \Codeception\Test\Unit
{
    public function testShouldReturnValueIfConstantIsDefined()
    {
        self::assertEquals(ActionMockA::class, Constant::getAction(Events::class . '::__MOCK__'));
    }

    public function testShouldReturnUndefinedActionIfConstantIsNotDefined()
    {
        self::assertEquals(UndefinedEvent::class, Constant::getAction(Events::class . '::TEST'));
    }
}
