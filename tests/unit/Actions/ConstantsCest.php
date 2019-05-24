<?php

use MVF\Servicer\Actions\ActionMock;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\Events;

class ConstantsCest
{
    public function shouldReturnValueIfConstantIsDefined(UnitTester $I)
    {
        $I->assertEquals(ActionMock::class, Constant::getAction(Events::class . '::__MOCK__'));
    }

    public function shouldReturnUndefinedActionIfConstantIsNotDefined(UnitTester $I)
    {
        $I->assertEquals('UNDEFINED_EVENT', Constant::getAction(Events::class . '::TEST'));
    }
}
