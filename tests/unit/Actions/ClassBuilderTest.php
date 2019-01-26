<?php

namespace MVF\Servicer\Action\Tests;

use AspectMock\Test;
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
}
