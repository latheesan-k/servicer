<?php namespace MVF\Servicer\Tests;

use Codeception\Stub\Expected;
use MVF\Servicer\Consumer;
use PHPUnit\Runner\Exception;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Tester\CommandTester;

class ConsumerTest extends \Codeception\Test\Unit
{
    /**
     * @var \MVF\Servicer\Tests\AcceptanceTester
     */
    protected $tester;

    public function testConsumerExecutesWithNoSpecifiedCommand()
    {
        $this->tester->runShellCommand('php index.php');
        $this->tester->seeResultCodeIs(0);
    }

    public function testHandleFunctionCallsApplicationRun()
    {
        $application = $this->makeEmpty(Application::class, ['run' => Expected::once()]);
        $consumer = $this->construct(Consumer::class, [], ['application' => $application]);
        $consumer->handle();
    }

    public function testApplicationShouldExitIfExceptionIsThrown()
    {
        $run = function () {
            throw new Exception('TEST');
        };

        $application = $this->makeEmpty(Application::class, ['run' => $run]);
        $consumer = $this->construct(Consumer::class, [], ['application' => $application]);

        $this->expectException(Exception::class);
        $consumer->handle();
    }
}