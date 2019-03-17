<?php

use MVF\Servicer\ActionInterface;
use MVF\Servicer\Events;
use MVF\Servicer\UndefinedEvent;

class EventsCest
{
    public function buildActionReturnsInvalidActionIfEventIsNotFound(UnitTester $I)
    {
        $test = function (string $message) use ($I) {
            $I->assertContains('Event is not defined', $message);
        };
        $I->mockBuilderFacadeBuildActionFor(UndefinedEvent::class, ['writeln' => $test]);

        $handler = $I->make(Events::class);
        $handler->triggerAction((object)['event' => 'TEST'], (object)[]);
    }

    public function buildActionReturnsValidAction(UnitTester $I)
    {
        $I->mockBuilderFacadeBuildActionFor(ActionInterface::class);
        $test = function (string $message) use ($I) {
            $I->assertContains('Event processed', $message);
        };

        $handler = $I->make(Events::class, ['writeln' => $test]);
        $handler->triggerAction((object)['event' => 'TEST'], (object)[]);
    }
}
