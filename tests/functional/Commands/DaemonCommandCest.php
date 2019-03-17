<?php

use Codeception\Stub\Expected;
use MVF\Servicer\Commands\DaemonCommand;
use MVF\Servicer\QueueInterface;
use PHPUnit\Runner\Exception;
use Symfony\Component\Console\Tester\CommandTester;

class DaemonCommandCest
{
    public function testCommandHandleCanExecuteWithoutQueues(FunctionalTester $I)
    {
        $consumer = $I->construct(DaemonCommand::class, [], ['delay' => 0]);
        $tester = new CommandTester($consumer);
        $tester->execute(['--once' => true]);
        $I->assertEquals('', $tester->getDisplay());
    }

    public function testQueueListenIsExecuted(FunctionalTester $I)
    {
        $queue = $I->makeEmpty(QueueInterface::class, ['listen' => Expected::once()]);
        $consumer = $I->construct(DaemonCommand::class, [$queue], ['delay' => 0]);
        $tester = new CommandTester($consumer);
        $tester->execute(['--once' => true]);
    }

    public function testThatExceptionsThrownByTheQueueAreHandled(FunctionalTester $I)
    {
        $listen = function () {
            throw new Exception('Something bad happened...');
        };

        $queue = $I->makeEmpty(QueueInterface::class, ['listen' => $listen]);
        $consumer = $I->construct(DaemonCommand::class, [$queue], ['delay' => 0]);
        $tester = new CommandTester($consumer);
        $tester->execute(['--once' => true]);
        $I->assertContains('Something bad happened', $tester->getDisplay());
    }
}
