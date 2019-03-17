<?php

use AspectMock\Test;
use Codeception\Stub\Expected;
use MVF\Servicer\Actions\ActionMockA;
use MVF\Servicer\Actions\ActionMockB;
use MVF\Servicer\Actions\ClassBuilder;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\UndefinedEvent;
use Symfony\Component\Console\Output\ConsoleOutput;

class ClassBuilderCest
{
    /**
     * @var ClassBuilder
     */
    private $builder;

    public function _before()
    {
        $this->builder = new ClassBuilder();
    }

    public function _after()
    {
        ClassBuilder::clean();
    }

    public function classBuilderReturnsUndefinedActionObject(UnitTester $I)
    {
        $I->assertInstanceOf(UndefinedEvent::class, $this->builder->buildActionFor('UNDEFINED'));
    }

    public function classBuilderReturnsSomeConstructedObject(UnitTester $I)
    {
        $I->mockGetAction(ActionMockA::class);
        $I->assertInstanceOf(ActionMockA::class, $this->builder->buildActionFor('TEST'));
    }

    public function classBuilderCanConstructInjections(UnitTester $I)
    {
        $I->mockGetAction([ActionMockB::class, [ConsoleOutput::class]]);
        $I->assertInstanceOf(ActionMockB::class, $this->builder->buildActionFor('TEST'));
    }

    public function classBuilderShouldConstructObjectIfArrayWithNoInjectionsIsProvided(UnitTester $I)
    {
        $I->mockGetAction([ActionMockA::class]);
        $I->assertInstanceOf(ActionMockA::class, $this->builder->buildActionFor('TEST'));
    }

    public function thereIsAWayToDefineInstanceObjectsForSpecificClasses(UnitTester $I)
    {
        ClassBuilder::setInstance(ActionMockA::class, new ActionMockA());
        $I->mockGetAction(ActionMockA::class);
        $I->expectExceptionMessage('action_mock_a', function () {
            $action = $this->builder->buildActionFor('TEST');
            $action->handle((object)[], (object)[]);
        });
    }

    public function thereIsAWayToDefineInstanceObjectsForSpecificInjection(UnitTester $I)
    {
        $consoleOutput = $I->make(ConsoleOutput::class, ['writeln' => Expected::once()]);
        ClassBuilder::setInstance(ConsoleOutput::class, $consoleOutput);
        $I->mockGetAction([ActionMockB::class, [ConsoleOutput::class]]);
        $action = $this->builder->buildActionFor('TEST');
        $action->handle((object)[], (object)[]);
    }
}
