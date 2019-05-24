<?php

use Codeception\Stub\Expected;
use MVF\Servicer\Actions\ActionMock;
use MVF\Servicer\Actions\ActionMockB;
use MVF\Servicer\Actions\ClassBuilder;
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

    public function classBuilderReturnsSomeConstructedObject(UnitTester $I)
    {
        $class = ActionMock::class;
        $I->assertInstanceOf(ActionMock::class, $this->builder->buildActionFor($class));
    }

    public function classBuilderCanConstructInjections(UnitTester $I)
    {
        $class = ['class' => ActionMockB::class, 'with' => [ConsoleOutput::class]];
        $I->assertInstanceOf(ActionMockB::class, $this->builder->buildActionFor($class));
    }

    public function classBuilderShouldConstructObjectIfArrayWithNoInjectionsIsProvided(UnitTester $I)
    {
        $class = ['class' => ActionMock::class];
        $I->assertInstanceOf(ActionMock::class, $this->builder->buildActionFor($class));
    }

    public function thereIsAWayToDefineInstanceObjectsForSpecificClasses(UnitTester $I)
    {
        $message = '';

        try {
            ClassBuilder::setInstance(ActionMockB::class, new ActionMock());
            $action = $this->builder->buildActionFor(ActionMockB::class);
            $action->handle([], []);
        } catch (Exception $exception) {
            $message = $exception->getMessage();
        }

        $I->assertStringStartsWith('action_mock_a', $message);
    }

    public function thereIsAWayToDefineInstanceObjectsForSpecificInjection(UnitTester $I)
    {
        $consoleOutput = $I->make(ConsoleOutput::class, ['writeln' => Expected::once()]);
        ClassBuilder::setInstance(ConsoleOutput::class, $consoleOutput);
        $class = ['class' => ActionMockB::class, 'with' => [ConsoleOutput::class]];
        $action = $this->builder->buildActionFor($class);
        $action->handle([], []);
    }
}
