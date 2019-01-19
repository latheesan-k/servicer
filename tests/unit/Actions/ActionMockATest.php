<?php

namespace MVF\Servicer\Action\Tests;

use MVF\Servicer\Actions\ActionMockA;

class ActionMockATest extends \Codeception\Test\Unit
{
    public function testClassBuilderReturnsUndefinedActionObject()
    {
        $mock = $this->make(ActionMockA::class);
        $mock->handle((object)[], (object)[]);
    }
}
