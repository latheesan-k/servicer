<?php

namespace MVF\Servicer\Action\Tests;

use AspectMock\Test;
use Codeception\Stub\Expected;
use MVF\Servicer\Actions\ActionMockA;
use MVF\Servicer\Actions\ActionMockB;
use MVF\Servicer\Actions\ClassBuilder;
use MVF\Servicer\Actions\Constant;
use MVF\Servicer\UndefinedEvent;
use Symfony\Component\Console\Output\ConsoleOutput;

class ClassBuilderTest extends \Codeception\Test\Unit
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
        Test::clean();
    }

    public function testClassBuilderReturnsUndefinedActionObject()
    {
        self::assertInstanceOf(UndefinedEvent::class, $this->builder->buildActionFor('UNDEFINED'));
    }

    public function testClassBuilderReturnsSomeConstructedObject()
    {
        Test::double(Constant::class, ['getAction' => ActionMockA::class]);
        self::assertInstanceOf(ActionMockA::class, $this->builder->buildActionFor('TEST'));
    }

    public function testClassBuilderCanConstructInjections()
    {
        $class = [ActionMockB::class, [ConsoleOutput::class]];
        Test::double(Constant::class, ['getAction' => $class]);
        self::assertInstanceOf(ActionMockB::class, $this->builder->buildActionFor('TEST'));
    }

    public function testClassBuilderShouldConstructObjectIfArrayWithNoInjectionsIsProvided()
    {
        $class = [ActionMockA::class];
        Test::double(Constant::class, ['getAction' => $class]);
        self::assertInstanceOf(ActionMockA::class, $this->builder->buildActionFor('TEST'));
    }

    public function testThatThereIsAWayToDefineInstanceObjectsForSpecificClasses()
    {
        $consoleOutput = $this->make(ActionMockA::class, ['writeln' => Expected::once()]);
        ClassBuilder::setInstance(ActionMockA::class, $consoleOutput);

        Test::double(Constant::class, ['getAction' => ActionMockA::class]);
        $action = $this->builder->buildActionFor('TEST');
        $action->handle((object)[], (object)[]);
    }

    public function testThatThereIsAWayToDefineInstanceObjectsForSpecificInjection()
    {
        $consoleOutput = $this->make(ConsoleOutput::class, ['writeln' => Expected::once()]);
        ClassBuilder::setInstance(ConsoleOutput::class, $consoleOutput);

        $class = [ActionMockB::class, [ConsoleOutput::class]];
        Test::double(Constant::class, ['getAction' => $class]);
        $action = $this->builder->buildActionFor('TEST');
        $action->handle((object)[], (object)[]);
    }
}
