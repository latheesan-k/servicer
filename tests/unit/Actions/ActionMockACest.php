<?php

use MVF\Servicer\Actions\ActionMockA;

class ActionMockACest
{
    public function classBuilderShouldReturnsUndefinedActionObject(UnitTester $I)
    {
        $I->expectExceptionMessage('action_mock_a', function () {
            (new ActionMockA())->handle((object)[], (object)[]);
        });
    }
}
