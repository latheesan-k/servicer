<?php

use MVF\Servicer\UndefinedEvent;

class UndefinedEventCest
{
    public function handlePrintsErrorMessage(UnitTester $I)
    {
        $I->expectExceptionMessage(
            'UndefinedAction handle should never be called',
            function () use ($I) {
                $action = $I->make(UndefinedEvent::class);
                $action->handle(['event' => 'TEST'], []);
            }
        );
    }
}
