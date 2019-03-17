<?php

use AspectMock\Test;
use MVF\Servicer\ActionInterface;
use MVF\Servicer\Actions\BuilderFacade;
use MVF\Servicer\Commands\ExecCommand;
use MVF\Servicer\Events;
use MVF\Servicer\Queues;
use Symfony\Component\Console\Tester\CommandTester;

class ExecCommandCest
{
    public function testThatEmptyBodyIsPassedToTheAction(FunctionalTester $I)
    {
        $handle = function (\stdClass $headers, \stdClass $body) use ($I) {
            $I->assertEquals((object)[], $body);
        };

        $action = $I->makeEmpty(ActionInterface::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $I->make(Queues::class, ['getHandlerClass' => Events::class]);
        $consumer = $I->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => '__MOCK__']);
    }

    public function testThatDefaultHeadersArePassedToTheAction(FunctionalTester $I)
    {
        $handle = function (\stdClass $headers, \stdClass $body) use ($I) {
            $I->assertEquals('__MOCK__', $headers->event);
        };

        $action = $I->makeEmpty(ActionInterface::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $I->make(Queues::class);
        $consumer = $I->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => 'test', ExecCommand::ACTION => '__MOCK__']);
    }

    public function testThatOptionalHeadersArePassedToTheAction(FunctionalTester $I)
    {
        $handle = function (\stdClass $headers, \stdClass $body) use ($I) {
            $I->assertEquals('john', $headers->name);
        };

        $action = $I->makeEmpty(ActionInterface::class, ['handle' => $handle]);
        Test::double(BuilderFacade::class, ['buildActionFor' => $action]);

        $eventHandlers = $I->make(Queues::class);
        $consumer = $I->construct(ExecCommand::class, [$eventHandlers]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::QUEUE => '__MOCK__', ExecCommand::ACTION => '__MOCK__', '-H' => ['name=john']]);
    }
}
