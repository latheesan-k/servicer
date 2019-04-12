<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use Codeception\Stub;
use MVF\Servicer\ConfigInterface;
use MVF\Servicer\Events;
use MVF\Servicer\Queues\SqsQueue;
use MVF\Servicer\SettingsInterface;

class Unit extends \Codeception\Module
{
    public function expectExceptionMessage($message, $function)
    {
        try {
            $function();
        } catch (\Exception $e) {
            $this->assertContains($message, $e->getMessage());
        }
    }

    public function expectActionHeaderToEqual(string $class, array $expected)
    {
        $beforeReceive = function ($receive) {
            $receive();
        };

        $settings = Stub::makeEmpty(SettingsInterface::class, ['beforeReceive' => $beforeReceive]);
        $triggerAction = function (array $headers, array $body) use (&$actual) {
            $actual = $headers;
        };

        $events = Stub::make(Events::class, ['triggerAction' => $triggerAction]);
        $config = Stub::makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new $class($config);
        $queue->listen();
        $this->assertEquals($expected, $actual);
    }

    public function expectActionBodyToEqual(string $class, array $expected)
    {
        $beforeReceive = function ($receive) {
            $receive();
        };

        $settings = Stub::makeEmpty(SettingsInterface::class, ['beforeReceive' => $beforeReceive]);
        $triggerAction = function (array $headers, array $body) use (&$actual) {
            $actual = $body;
        };

        $events = Stub::make(Events::class, ['triggerAction' => $triggerAction]);
        $config = Stub::makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new $class($config);
        $queue->listen();
        $this->assertEquals($expected, $actual);
    }

    public function expectActionToBeCalled(Stub\StubMarshaler $expected)
    {
        $settings = Stub::makeEmpty(SettingsInterface::class);
        $events = Stub::make(Events::class, ['triggerAction' => $expected]);
        $config = Stub::makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);
        $queue->listen();
    }
}
