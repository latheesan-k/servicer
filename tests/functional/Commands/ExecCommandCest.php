<?php

use AspectMock\Test;
use MVF\Servicer\Actions\ActionMock;
use MVF\Servicer\Actions\BuilderFacade;
use MVF\Servicer\Commands\ExecCommand;
use MVF\Servicer\Events;
use MVF\Servicer\Queues;
use Symfony\Component\Console\Tester\CommandTester;

class ExecCommandCest
{
    public function testThatEmptyBodyIsPassedToTheAction(FunctionalTester $I)
    {
        $handle = function (array $headers, array $body) use ($I, &$eventBody) {
            $eventBody = $body;
        };

        $action = $I->make(ActionMock::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $I->make(Queues::class, ['getClass' => Events::class]);
        $consumer = $I->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => '__MOCK__']);
        $I->assertEquals([], $eventBody);
    }

    public function testThatDefaultHeadersArePassedToTheAction(FunctionalTester $I)
    {
        $handle = function (array $headers, array $body) use (&$event) {
            $event = $headers['event'];
        };

        $action = $I->make(ActionMock::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $I->make(Queues::class, ['getClass' => Events::class]);
        $consumer = $I->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => '__MOCK__']);
        $I->assertEquals('__MOCK__', $event);
    }

    public function testThatOptionalHeadersArePassedToTheAction(FunctionalTester $I)
    {
        $handle = function (array $headers, array $body) use (&$name) {
            $name = $headers['name'];
        };

        $action = $I->make(ActionMock::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $I->make(Queues::class, ['getClass' => Events::class]);
        $consumer = $I->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => '__MOCK__', ExecCommand::ACTION => '__MOCK__', '-H' => ['name=john']]);
        $I->assertEquals('john', $name);
    }
}
