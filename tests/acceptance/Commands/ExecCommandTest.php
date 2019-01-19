<?php namespace MVF\Servicer\Tests;

use Codeception\Stub\Expected;
use MVF\Servicer\Commands\DaemonCommand;
use MVF\Servicer\Commands\ExecCommand;
use MVF\Servicer\Consumer;
use MVF\Servicer\EventInterface;
use MVF\Servicer\QueueInterface;
use PHPUnit\Runner\Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Tester\CommandTester;

class ExecCommandTest extends \Codeception\Test\Unit
{
    /**
     * @var \MVF\Servicer\Tests\AcceptanceTester
     */
    protected $tester;

    public function testCommandHandleCanExecuteWithoutQueues()
    {
        $consumer = $this->construct(ExecCommand::class);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::ACTION => 'MOCK']);
        self::assertEquals('', $tester->getDisplay());
    }

    public function testThatEmptyBodyIsPassedToTheAction()
    {
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals((object)[], $body);
        };

        $events = $this->makeEmpty(EventInterface::class, ['triggerAction' => $triggerAction]);
        $queue = $this->makeEmpty(QueueInterface::class, ['getEvents' => $events]);
        $consumer = $this->construct(ExecCommand::class, [$queue]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::ACTION => 'MOCK']);
    }

    public function testThatDefaultHeadersArePassedToTheAction()
    {
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals((object)['event' => 'MOCK'], $headers);
        };

        $events = $this->makeEmpty(EventInterface::class, ['triggerAction' => $triggerAction]);
        $queue = $this->makeEmpty(QueueInterface::class, ['getEvents' => $events]);
        $consumer = $this->construct(ExecCommand::class, [$queue]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::ACTION => 'MOCK']);
    }

    public function testThatOptionalHeadersArePassedToTheAction()
    {
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('john', $headers->name);
        };

        $events = $this->makeEmpty(EventInterface::class, ['triggerAction' => $triggerAction]);
        $queue = $this->makeEmpty(QueueInterface::class, ['getEvents' => $events]);
        $consumer = $this->construct(ExecCommand::class, [$queue]);
        $tester = new CommandTester($consumer);
        $tester->execute([ExecCommand::ACTION => 'MOCK', '-H' => ['name=john']]);
    }
}