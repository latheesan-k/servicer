<?php

use MVF\Servicer\Events;

class EventsCest
{
    public function buildActionReturnsInvalidActionIfEventIsNotFound(UnitTester $I)
    {
        $test = function (string $message) use ($I) {
            $I->assertContains('Event is not defined', $message);
        };
        $I->mockBuildActionFor(['writeln' => $test]);

        $handler = $I->make(Events::class);
        $handler->triggerAction((object)['event' => 'TEST'], (object)[]);
    }

    public function buildActionReturnsValidAction(UnitTester $I)
    {
        $I->mockBuildActionFor();
        $test = function (string $message) use ($I) {
            $I->assertContains('Event processed', $message);
        };

        $handler = $I->make(Events::class, ['writeln' => $test]);
        $handler->triggerAction((object)['event' => 'TEST'], (object)[]);
    }
}
