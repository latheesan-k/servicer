<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 19/01/19
 * Time: 12:35.
 */

namespace MVF\Servicer\Tests\Queues;

use AspectMock\Test;
use Aws\Result;
use Codeception\Stub\Expected;
use MVF\Servicer\Clients\SqsClient;
use MVF\Servicer\ConfigInterface;
use MVF\Servicer\Events;
use MVF\Servicer\Queues\SqsQueue;
use MVF\Servicer\SettingsInterface;

class SqsQueueTest extends \Codeception\Test\Unit
{
    private $messages;

    public function _after()
    {
        Test::clean();
    }

    public function _before()
    {
        $attributes = ['SentTimestamp' => 0];
        $this->messages = [
            ['ReceiptHandle' => 'test', 'Attributes' => $attributes],
        ];
    }

    public function testCircuitBreakerWorks()
    {
        $settings = $this->makeEmpty(SettingsInterface::class, ['isCircuitBreakerClosed' => true]);
        $events = $this->make(Events::class);
        $config = $this->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $client = $this->make(SqsClient::class, ['receiveMessage' => Expected::never()]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatHeadersArePassedToAction()
    {
        $settings = $this->makeEmpty(SettingsInterface::class);
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('1.0.0', $headers->version);
        };

        $events = $this->make(Events::class, ['triggerAction' => $triggerAction]);
        $config = $this->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $this->messages[0]['MessageAttributes'] = [
            'Version' => ['DataType' => 'String', 'StringValue' => '1.0.0'],
        ];

        $result = $this->make(Result::class, ['get' => $this->messages]);
        $client = $this->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatEmptyHeadersAreAlwaysPassedToAction()
    {
        $settings = $this->makeEmpty(SettingsInterface::class);
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals((object)[], $headers);
        };

        $events = $this->make(Events::class, ['triggerAction' => $triggerAction]);
        $config = $this->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $result = $this->make(Result::class, ['get' => $this->messages]);
        $client = $this->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testCaseWhereNoMessagesWereReceived()
    {
        $settings = $this->makeEmpty(SettingsInterface::class);
        $events = $this->make(Events::class, ['triggerAction' => Expected::never()]);
        $config = $this->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $result = $this->make(Result::class, ['get' => null]);
        $client = $this->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatBodyIsPassedToAction()
    {
        $settings = $this->makeEmpty(SettingsInterface::class);
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('john', $body->name);
        };

        $events = $this->make(Events::class, ['triggerAction' => $triggerAction]);
        $config = $this->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $this->messages[0]['Body'] = '{"name":"john"}';
        $result = $this->make(Result::class, ['get' => $this->messages]);
        $client = $this->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatEmptyBodyIsAlwaysPassedToAction()
    {
        $settings = $this->makeEmpty(SettingsInterface::class);
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals((object)[], $body);
        };

        $events = $this->make(Events::class, ['triggerAction' => $triggerAction]);
        $config = $this->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $result = $this->make(Result::class, ['get' => $this->messages]);
        $client = $this->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatActionIsNotTriggeredIfEventIsOld()
    {
        $settings = $this->makeEmpty(SettingsInterface::class, ['isOldMessage' => true]);
        $events = $this->make(Events::class, ['triggerAction' => Expected::never()]);
        $config = $this->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $result = $this->make(Result::class, ['get' => $this->messages]);
        $client = $this->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }
}
