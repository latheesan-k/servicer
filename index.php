<?php

include __DIR__.'/vendor/autoload.php';

use MVF\Servicer\Commands\ExecCommand;
use Symfony\Component\Console\Application;

class TestAction extends \MVF\Servicer\StandardConditions
{
    /**
     * Executes the action.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body Body of the event
     */
    public function handle(\stdClass $headers, \stdClass $body): void
    {
        dump($headers, $body);
    }
}

class TestEvents extends \MVF\Servicer\Events
{
    const TEST = TestAction::class;
}

class Queues extends \MVF\Servicer\Queues
{
    const TEST_EVENTS = TestEvents::class;
}

class Settings implements \MVF\Servicer\SettingsInterface
{

    public function getName(): string
    {
        return 'test';
    }

    public function isCircuitBreakerClosed(): bool
    {
        return false;
    }

    public function isOldMessage(int $timestamp, \stdClass $headers, callable $consumeMessage): void
    {
        $consumeMessage();
    }
}

class Config implements \MVF\Servicer\ConfigInterface
{
    public function getSettings(): \MVF\Servicer\SettingsInterface
    {
        return new Settings();
    }

    public function getEvents(): \MVF\Servicer\Events
    {
        return new TestEvents();
    }
}

$app = new Application();

$exec = new ExecCommand(new Queues());
$daemon = new \MVF\Servicer\Commands\DaemonCommand(
    new \MVF\Servicer\Queues\SqsQueue(new Config())
);

$app->addCommands([$exec, $daemon]);
$app->run();
