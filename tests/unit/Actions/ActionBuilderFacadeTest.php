<?php

namespace MVF\Servicer\Action\Tests;

use MVF\Servicer\ActionInterface;
use MVF\Servicer\Actions\ActionBuilderFacade;

class ActionBuilderFacadeTest extends \Codeception\Test\Unit
{
    public function testActionBuilderReturnsSomeAction()
    {
        self::assertInstanceOf(ActionInterface::class, ActionBuilderFacade::buildActionFor('TEST'));
    }
}
