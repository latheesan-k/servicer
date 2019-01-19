<?php

namespace MVF\Servicer\Action\Tests;

use MVF\Servicer\Actions\ActionMockB;

class ActionMockBTest extends \Codeception\Test\Unit
{
    public function testClassBuilderReturnsUndefinedActionObject()
    {
        $mock = $this->make(ActionMockB::class);
        $mock->handle((object)[], (object)[]);
    }
}
