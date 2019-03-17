<?php

use MVF\Servicer\ActionInterface;
use MVF\Servicer\Actions\BuilderFacade;

class ActionBuilderFacadeCest
{
    public function actionBuilderReturnsSomeAction(UnitTester $I)
    {
        $I->assertInstanceOf(ActionInterface::class, BuilderFacade::buildActionFor('TEST'));
    }
}
