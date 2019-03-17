<?php

use MVF\Servicer\Actions\ActionMockA;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\Events;
use MVF\Servicer\UndefinedEvent;

class ConstantsCest
{
    public function shouldReturnValueIfConstantIsDefined(UnitTester $I)
    {
        $I->assertEquals(ActionMockA::class, Constant::getAction(Events::class . '::__MOCK__'));
    }

    public function shouldReturnUndefinedActionIfConstantIsNotDefined(UnitTester $I)
    {
        $I->assertEquals(UndefinedEvent::class, Constant::getAction(Events::class . '::TEST'));
    }
}
