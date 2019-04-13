<?php

use Codeception\Stub\Expected;
use MVF\Servicer\Actions\ActionMock;
use MVF\Servicer\Actions\ActionMockB;
use MVF\Servicer\Actions\ClassBuilder;
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
        $I->mockGetAction(ActionMock::class);
        $I->assertInstanceOf(ActionMock::class, $this->builder->buildActionFor('TEST'));
    }

    public function classBuilderCanConstructInjections(UnitTester $I)
    {
        $I->mockGetAction([ActionMockB::class, [ConsoleOutput::class]]);
        $I->assertInstanceOf(ActionMockB::class, $this->builder->buildActionFor('TEST'));
    }

    public function classBuilderShouldConstructObjectIfArrayWithNoInjectionsIsProvided(UnitTester $I)
    {
        $I->mockGetAction([ActionMock::class]);
        $I->assertInstanceOf(ActionMock::class, $this->builder->buildActionFor('TEST'));
    }

    public function thereIsAWayToDefineInstanceObjectsForSpecificClasses(UnitTester $I)
    {
        ClassBuilder::setInstance(ActionMock::class, new ActionMock());
        $I->mockGetAction(ActionMock::class);
        $I->expectExceptionMessage(
            'action_mock_a',
            function () {
                $action = $this->builder->buildActionFor('TEST');
                $action->handle([], []);
            }
        );
    }

    public function thereIsAWayToDefineInstanceObjectsForSpecificInjection(UnitTester $I)
    {
        $consoleOutput = $I->make(ConsoleOutput::class, ['writeln' => Expected::once()]);
        ClassBuilder::setInstance(ConsoleOutput::class, $consoleOutput);
        $I->mockGetAction([ActionMockB::class, [ConsoleOutput::class]]);
        $action = $this->builder->buildActionFor('TEST');
        $action->handle([], []);
    }
}
