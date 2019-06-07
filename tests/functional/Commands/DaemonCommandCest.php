<?php

use Codeception\Stub\Expected;
use MVF\Servicer\Commands\DaemonCommand;
use MVF\Servicer\QueueInterface;
use MVF\Servicer\Queues\SqsQueue;
use PHPUnit\Runner\Exception;
use Symfony\Component\Console\Tester\CommandTester;

class DaemonCommandCest
{
    public function testCommandHandleCanExecuteWithoutQueues(FunctionalTester $I)
    {
        $queue = ['test' => $I->makeEmpty(SqsQueue::class)];
        $consumer = $I->construct(DaemonCommand::class, [$queue], ['delay' => 0]);
        $tester = new CommandTester($consumer);
        $tester->execute(['queue' => 'test']);
        $I->assertEquals('', $tester->getDisplay());
    }

    public function testQueueListenIsExecuted(FunctionalTester $I)
    {
        $queue = ['test' => $I->makeEmpty(QueueInterface::class, ['listen' => Expected::once()])];
        $consumer = $I->construct(DaemonCommand::class, [$queue], ['delay' => 0]);
        $tester = new CommandTester($consumer);
        $tester->execute(['queue' => 'test']);
    }

    public function testThatExceptionsThrownByTheQueueAreHandled(FunctionalTester $I)
    {
        $listen = function () {
            throw new Exception('Something bad happened...');
        };
        $queue = ['test' => $I->makeEmpty(QueueInterface::class, ['listen' => $listen])];
        $consumer = $I->construct(DaemonCommand::class, [$queue], ['delay' => 0]);
        $tester = new CommandTester($consumer);
        $tester->execute(['queue' => 'test']);
        $I->assertContains('Something bad happened', $tester->getDisplay());
    }
}
