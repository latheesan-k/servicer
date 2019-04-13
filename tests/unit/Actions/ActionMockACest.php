<?php

use MVF\Servicer\Actions\ActionMock;

class ActionMockACest
{
    public function classBuilderShouldReturnsUndefinedActionObject(UnitTester $I)
    {
        $I->expectExceptionMessage(
            'action_mock_a',
            function () {
                (new ActionMock())->handle([], []);
            }
        );
    }
}
