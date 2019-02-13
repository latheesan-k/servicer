<?php

namespace MVF\Servicer\Tests;

use AspectMock\Test;
use MVF\Servicer\ActionInterface;
use MVF\Servicer\Actions\BuilderFacade;
use MVF\Servicer\Builder;
use MVF\Servicer\Commands\ExecCommand;
use MVF\Servicer\Events;
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
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $this->make(Builder::class, ['getHandlerClass' => Events::class]);
        $consumer = $this->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => '__MOCK__']);
    }

    public function testThatDefaultHeadersArePassedToTheAction()
    {
        $handle = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('__MOCK__', $headers->event);
        };

        $action = $this->makeEmpty(ActionInterface::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $this->make(Builder::class);
        $consumer = $this->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => '__MOCK__']);
    }

    public function testThatOptionalHeadersArePassedToTheAction()
    {
        $handle = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('john', $headers->name);
        };

        $action = $this->makeEmpty(ActionInterface::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $this->make(Builder::class);
        $consumer = $this->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => '__MOCK__', ExecCommand::ACTION => '__MOCK__', '-H' => ['name=john']]);
    }
}
