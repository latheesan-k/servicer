<?php

namespace MVF\Servicer\Tests;

use Codeception\Stub\Expected;
use MVF\Servicer\Commands\DaemonCommand;
use MVF\Servicer\QueueInterface;
use PHPUnit\Runner\Exception;
use Symfony\Component\Console\Tester\CommandTester;

class DaemonCommandTest extends \Codeception\Test\Unit
{
    /**
     * @var \MVF\Servicer\Tests\AcceptanceTester
     */
    protected $tester;

    public function testCommandHandleCanExecuteWithoutQueues()
    {
        $consumer = $this->construct(DaemonCommand::class, [], ['delay' => 0]);
        $tester = new CommandTester($consumer);
        $tester->execute(['--once' => true]);
        self::assertEquals('', $tester->getDisplay());
    }

    public function testQueueListenIsExecuted()
    {
        $queue = $this->makeEmpty(QueueInterface::class, ['listen' => Expected::once()]);
        $consumer = $this->construct(DaemonCommand::class, [$queue], ['delay' => 0]);
        $tester = new CommandTester($consumer);
        $tester->execute(['--once' => true]);
    }

    public function testThatExceptionsThrownByTheQueueAreHandled()
    {
        $listen = function () {
            throw new Exception('Something bad happened...');
        };

        $queue = $this->makeEmpty(QueueInterface::class, ['listen' => $listen]);
        $consumer = $this->construct(DaemonCommand::class, [$queue], ['delay' => 0]);
        $tester = new CommandTester($consumer);
        $tester->execute(['--once' => true]);
        self::assertContains('Something bad happened', $tester->getDisplay());
    }
}
