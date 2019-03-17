<?php

use MVF\Servicer\StandardConditions;

class StandardConditionsCest
{
    private $wasCalled = false;

    public function messageIsConsumed(UnitTester $I)
    {
        $consumeMessage = function () {
            $this->wasCalled = true;
        };

        $action = $I->make(StandardConditions::class);
        $action->beforeReceive((object)[], $consumeMessage);
        $I->assertEquals(true, $this->wasCalled);
    }
}
