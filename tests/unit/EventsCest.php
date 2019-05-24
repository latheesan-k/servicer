<?php

use MVF\Servicer\ActionInterface;
use MVF\Servicer\Events;

class EventsCest
{
    public function buildActionReturnsInvalidActionIfEventIsNotFound(UnitTester $I)
    {
        $test = function (string $message) use ($I) {
            $I->assertContains('Event is not defined', $message);
        };
        $I->mockBuilderFacadeBuildActionFor(ActionInterface::class, ['writeln' => $test]);

        $handler = $I->make(Events::class);
        $handler->triggerAction(['event' => 'TEST'], []);
    }

    public function buildActionReturnsValidAction(UnitTester $I)
    {
        $I->mockBuilderFacadeBuildActionFor(ActionInterface::class);
        $test = function (string $message) use ($I) {
            static $count = 0;
            static $cases = ['STARTED', 'COMPLETED'];
            $I->assertContains($cases[$count++], $message);
        };

        $handler = $I->make(Events::class, ['writeln' => $test]);
        $handler->triggerAction(['event' => '__MOCK__'], []);
    }
}
