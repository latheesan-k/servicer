<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 19/01/19
 * Time: 12:35.
 */

use AspectMock\Test;
use Aws\Result;
use Codeception\Stub\Expected;
use MVF\Servicer\Clients\SqsClient;
use MVF\Servicer\ConfigInterface;
use MVF\Servicer\Events;
use MVF\Servicer\Queues\SqsQueue;
use MVF\Servicer\SettingsInterface;

class SqsQueueCest
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

    public function testCircuitBreakerWorks(UnitTester $I)
    {
        $settings = $I->makeEmpty(SettingsInterface::class, ['isCircuitBreakerClosed' => true]);
        $events = $I->make(Events::class);
        $config = $I->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $client = $I->make(SqsClient::class, ['receiveMessage' => Expected::never()]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatHeadersArePassedToAction(UnitTester $I)
    {
        $settings = $I->makeEmpty(SettingsInterface::class);
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('1.0.0', $headers->version);
        };

        $events = $I->make(Events::class, ['triggerAction' => $triggerAction]);
        $config = $I->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $this->messages[0]['MessageAttributes'] = [
            'Version' => ['DataType' => 'String', 'StringValue' => '1.0.0'],
        ];

        $result = $I->make(Result::class, ['get' => $this->messages]);
        $client = $I->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatEmptyHeadersAreAlwaysPassedToAction(UnitTester $I)
    {
        $settings = $I->makeEmpty(SettingsInterface::class);
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals((object)[], $headers);
        };

        $events = $I->make(Events::class, ['triggerAction' => $triggerAction]);
        $config = $I->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $result = $I->make(Result::class, ['get' => $this->messages]);
        $client = $I->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testCaseWhereNoMessagesWereReceived(UnitTester $I)
    {
        $settings = $I->makeEmpty(SettingsInterface::class);
        $events = $I->make(Events::class, ['triggerAction' => Expected::never()]);
        $config = $I->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $result = $I->make(Result::class, ['get' => null]);
        $client = $I->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatBodyIsPassedToAction(UnitTester $I)
    {
        $settings = $I->makeEmpty(SettingsInterface::class);
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('john', $body->name);
        };

        $events = $I->make(Events::class, ['triggerAction' => $triggerAction]);
        $config = $I->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $this->messages[0]['Body'] = '{"name":"john"}';
        $result = $I->make(Result::class, ['get' => $this->messages]);
        $client = $I->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatEmptyBodyIsAlwaysPassedToAction(UnitTester $I)
    {
        $settings = $I->makeEmpty(SettingsInterface::class);
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals((object)[], $body);
        };

        $events = $I->make(Events::class, ['triggerAction' => $triggerAction]);
        $config = $I->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $result = $I->make(Result::class, ['get' => $this->messages]);
        $client = $I->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatActionIsNotTriggeredIfEventIsOld(UnitTester $I)
    {
        $isOldMessage = function ($headers, callable $consumeMessage) {
            $consumeMessage();
        };
        $settings = $I->makeEmpty(SettingsInterface::class, ['isOldMessage' => $isOldMessage]);
        $events = $I->make(Events::class, ['triggerAction' => Expected::once()]);
        $config = $I->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $result = $I->make(Result::class, ['get' => $this->messages]);
        $client = $I->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatMessagesFromSNSHaveCorrectHeaders(UnitTester $I)
    {
        $settings = $I->makeEmpty(SettingsInterface::class);
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('twitter', $headers->platform);
        };

        $events = $I->make(Events::class, ['triggerAction' => $triggerAction]);
        $config = $I->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $this->messages[0]['Body'] = [
            'Type' => 'Notification',
            'MessageAttributes' => ['platform' => ['Type' => 'String', 'Value' => 'twitter']],
        ];

        $result = $I->make(Result::class, ['get' => $this->messages]);
        $client = $I->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }

    public function testThatMessagesFromSNSHaveCorrectBody(UnitTester $I)
    {
        $settings = $I->makeEmpty(SettingsInterface::class);
        $triggerAction = function (\stdClass $headers, \stdClass $body) {
            self::assertEquals('hola', $body->message);
        };

        $events = $I->make(Events::class, ['triggerAction' => $triggerAction]);
        $config = $I->makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);

        $this->messages[0]['Body'] = [
            'Type' => 'Notification',
            'Message' => '{"message": "hola"}',
        ];

        $result = $I->make(Result::class, ['get' => $this->messages]);
        $client = $I->makeEmpty(SqsClient::class, ['receiveMessage' => $result]);
        Test::double(SqsClient::class, ['instance' => $client]);
        $queue->listen();
    }
}
