<?php

namespace MVF\Servicer\Tests;

use AspectMock\Test;
use MVF\Servicer\ActionInterface;
use MVF\Servicer\Actions\ActionBuilderFacade;
use MVF\Servicer\Commands\ExecCommand;
use MVF\Servicer\EventHandler;
use MVF\Servicer\EventHandlersInterface;
use Symfony\Component\Console\Tester\CommandTester;

class ExecCommandTest extends \Codeception\Test\Unit
{
    /**
     * @var \MVF\Servicer\Tests\AcceptanceTester
     */
    protected $tester;

    public function testThatEmptyBodyIsPassedToTheAction()
    {
        $handle = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals((object)[], $body);
        };

        $action = $this->makeEmpty(ActionInterface::class, ['handle' => $handle]);
        Test::double(ActionBuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $this->makeEmpty(
            EventHandlersInterface::class,
            ['getEventHandler' => EventHandler::class]
        );

        $consumer = $this->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => 'MOCK']);
    }

    public function testThatDefaultHeadersArePassedToTheAction()
    {
        $handle = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('MOCK', $headers->event);
        };

        $action = $this->makeEmpty(ActionInterface::class, ['handle' => $handle]);
        Test::double(ActionBuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $this->makeEmpty(
            EventHandlersInterface::class,
            ['getEventHandler' => EventHandler::class]
        );

        $consumer = $this->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => 'MOCK']);
    }

    public function testThatOptionalHeadersArePassedToTheAction()
    {
        $handle = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('john', $headers->name);
        };

        $action = $this->makeEmpty(ActionInterface::class, ['handle' => $handle]);
        Test::double(ActionBuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $this->makeEmpty(
            EventHandlersInterface::class,
            ['getEventHandler' => EventHandler::class]
        );

        $consumer = $this->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => 'MOCK', '-H' => ['name=john']]);
    }
}
