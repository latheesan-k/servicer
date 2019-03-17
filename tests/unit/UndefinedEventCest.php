<?php

use MVF\Servicer\UndefinedEvent;

class UndefinedEventCest
{
    public function handlePrintsErrorMessage(UnitTester $I)
    {
        $I->expectException(\Exception::class, function () use ($I) {
            $action = $I->make(UndefinedEvent::class);
            $action->handle((object)['event' => 'TEST'], (object)[]);
        });
    }
}
