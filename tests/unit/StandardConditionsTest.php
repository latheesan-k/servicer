<?php

namespace MVF\Servicer\Tests;

use MVF\Servicer\StandardConditions;

class StandardConditionsTest extends \Codeception\Test\Unit
{
    private $wasCalled = false;

    public function testMessageIsConsumed()
    {
        $consumeMessage = function () {
            $this->wasCalled = true;
        };

        $action = $this->make(StandardConditions::class);
        $action->skipMessage(0, (object)[], $consumeMessage);
        self::assertEquals(true, $this->wasCalled);
    }
}
