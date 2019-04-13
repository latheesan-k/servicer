<?php

include __DIR__.'/vendor/autoload.php';

use MVF\Servicer\Commands\ExecCommand;
use Symfony\Component\Console\Application;

class TestAction implements \MVF\Servicer\ActionInterface
{
    use \MVF\Servicer\AlwaysConsumeAction;

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

    public function beforeReceive(callable $receive): void
    {
        $receive();
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
