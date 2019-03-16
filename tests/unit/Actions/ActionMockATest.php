<?php

namespace MVF\Servicer\Action\Tests;

use MVF\Servicer\Actions\ActionMockA;

class ActionMockATest extends \Codeception\Test\Unit
{
    public function testClassBuilderReturnsUndefinedActionObject()
    {
        $this->expectExceptionMessage('action_mock_a');
        (new ActionMockA())->handle((object)[], (object)[]);
    }
}
