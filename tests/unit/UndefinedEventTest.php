<?php

namespace MVF\Servicer\Tests;

use MVF\Servicer\UndefinedEvent;

class UndefinedEventTest extends \Codeception\Test\Unit
{
    public function testHandlePrintsErrorMessage()
    {
        $this->expectException(\Exception::class);
        $action = $this->make(UndefinedEvent::class);
        $action->handle((object)['event' => 'TEST'], (object)[]);
    }
}
