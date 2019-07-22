<?php

include __DIR__.'/vendor/autoload.php';

use MVF\Servicer\ActionInterface;
use MVF\Servicer\AlwaysConsumeAction;
use MVF\Servicer\Commands\DaemonCommand;
use MVF\Servicer\Commands\ExecCommand;
use MVF\Servicer\ConfigInterface;
use MVF\Servicer\Events;
use MVF\Servicer\Queues\SqsQueue;
use MVF\Servicer\Services\LogCapsule;
use MVF\Servicer\SettingsInterface;
use Symfony\Component\Console\Application;

class TestAction implements ActionInterface
{
    use AlwaysConsumeAction;

    /**
     * Executes the action.
     *
     * @param array $headers Headers of the event
     * @param array $body Body of the event
     */
    public function handle(array $headers, array $body): void
    {
        dump($headers, $body);
    }
}

class TestEvents extends Events
{
    const TEST = [TestAction::class];
}

class Queues extends \MVF\Servicer\Queues
{
    const TEST_EVENTS = TestEvents::class;
}

class Settings implements SettingsInterface
{

    public function getName(): string
    {
        return 'test';
    }

    public function beforeReceive(callable $receive): void
    {
        $receive();
    }
}

class Config implements ConfigInterface
{
    public function getSettings(): SettingsInterface
    {
        return new Settings();
    }

    public function getEvents(): Events
    {
        return new TestEvents();
    }
}

$app = new Application();
LogCapsule::setup($app);

$exec = new ExecCommand(new Queues());
$daemon = new DaemonCommand(
    [
        'test' => new SqsQueue(new Config())
    ]
);

$app->addCommands([$exec, $daemon]);
$app->run();
