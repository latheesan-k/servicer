<?php

use AspectMock\Test;
use MVF\Servicer\ActionInterface;
use MVF\Servicer\Actions\BuilderFacade;
use MVF\Servicer\Commands\ExecCommand;
use MVF\Servicer\Events;
use MVF\Servicer\Queues;
use MVF\Servicer\StandardConditions;
use Symfony\Component\Console\Tester\CommandTester;

class ExecCommandCest
{
    public function testThatEmptyBodyIsPassedToTheAction(FunctionalTester $I)
    {
        $handle = function (\stdClass $headers, \stdClass $body) use ($I, &$eventBody) {
            $eventBody = $body;
        };

        $action = $I->make(StandardConditions::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $I->make(Queues::class, ['getHandlerClass' => Events::class]);
        $consumer = $I->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => '__MOCK__']);
        $I->assertEquals((object)[], $eventBody);
    }

    public function testThatDefaultHeadersArePassedToTheAction(FunctionalTester $I)
    {
        $handle = function (\stdClass $headers, \stdClass $body) use ($I, &$event) {
            $event = $headers->event;
        };

        $action = $I->make(StandardConditions::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $I->make(Queues::class);
        $consumer = $I->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => '__MOCK__']);
        $I->assertEquals('__MOCK__', $event);
    }

    public function testThatOptionalHeadersArePassedToTheAction(FunctionalTester $I)
    {
        $handle = function (\stdClass $headers, \stdClass $body) use ($I, &$name) {
            $name = $headers->name;
        };

        $action = $I->make(StandardConditions::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $I->make(Queues::class);
        $consumer = $I->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => '__MOCK__', ExecCommand::ACTION => '__MOCK__', '-H' => ['name=john']]);
        $I->assertEquals('john', $name);
    }
}
