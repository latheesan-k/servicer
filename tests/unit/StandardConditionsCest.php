<?php

use MVF\Servicer\StandardConditions;

class StandardConditionsCest
{
    private $wasCalled = false;

    public function messageIsConsumed(UnitTester $I)
    {
        $consume = function () {
            $this->wasCalled = true;
        };

        $action = $I->make(StandardConditions::class);
        $action->beforeAction([], [], $consume);
        $I->assertEquals(true, $this->wasCalled);
    }
}
