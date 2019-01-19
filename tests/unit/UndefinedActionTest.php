<?php

namespace MVF\Servicer\Tests;

use MVF\Servicer\UndefinedAction;

class UndefinedActionTest extends \Codeception\Test\Unit
{
    public function testHandlePrintsErrorMessage()
    {
        $test = function (string $message) {
            $this->assertContains('TEST', $message);
        };

        $action = $this->make(UndefinedAction::class, ['writeln' => $test]);
        $action->handle((object)['event' => 'TEST'], (object)[]);
    }
}
