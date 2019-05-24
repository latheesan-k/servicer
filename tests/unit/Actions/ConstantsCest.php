<?php

use MVF\Servicer\Actions\ActionMock;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\Events;

class ConstantsCest
{
    public function shouldReturnValueIfConstantIsDefined(UnitTester $I)
    {
        $I->assertEquals([ActionMock::class], Constant::getActions(Events::class . '::__MOCK__'));
    }

    public function shouldReturnUndefinedActionIfConstantIsNotDefined(UnitTester $I)
    {
        $I->assertEquals([], Constant::getActions(Events::class . '::TEST'));
    }
}
