<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 19/01/19
 * Time: 12:35.
 */

use AspectMock\Test;
use Codeception\Stub;
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

    public function headersShouldBeParsedAndPassedToAction(UnitTester $I)
    {
        $this->messages[0]['MessageAttributes'] = [
            'Version' => ['DataType' => 'String', 'StringValue' => '1.0.0'],
        ];

        $I->mockSqsClientInstance($this->messages);
        $I->expectActionHeaderToEqual(SqsQueue::class, ['version' => '1.0.0']);
    }

    public function emptyHeadersAreAlwaysPassedToAction(UnitTester $I)
    {
        $I->mockSqsClientInstance($this->messages);
        $I->expectActionHeaderToEqual(SqsQueue::class, []);
    }

    public function bodyShouldBeParsedAndPassedToAction(UnitTester $I)
    {
        $this->messages[0]['Body'] = '{"name":"john"}';
        $I->mockSqsClientInstance($this->messages);
        $I->expectActionBodyToEqual(SqsQueue::class, ['name' => 'john']);
    }

    public function emptyBodyShouldAlwaysBePassedToAction(UnitTester $I)
    {
        $I->mockSqsClientInstance($this->messages);
        $I->expectActionBodyToEqual(SqsQueue::class, []);
    }

    public function messagesFromSnsShouldHaveCorrectHeaders(UnitTester $I)
    {
        $this->messages[0]['Body'] = '{"Type":"Notification","MessageAttributes":{"platform":{"Type":"String","Value":"twitter"}}}';
        $I->mockSqsClientInstance($this->messages);
        $I->expectActionHeaderToEqual(SqsQueue::class, ['platform' => 'twitter']);
    }

    public function messagesFromSnsShouldHaveCorrectBody(UnitTester $I)
    {
        $this->messages[0]['Body'] = '{"Type":"Notification","Message": "{\"message\":\"hola\"}"}';
        $I->mockSqsClientInstance($this->messages);
        $I->expectActionBodyToEqual(SqsQueue::class, ['message' => 'hola']);
    }

    public function messagesShouldWorkIfNoMessageIsReceived(UnitTester $I)
    {
        $I->mockSqsClientInstance(null);
        $beforeReceive = function ($receive) {
            $receive();
        };

        $settings = Stub::makeEmpty(SettingsInterface::class, ['beforeReceive' => $beforeReceive]);
        $called = false;
        $triggerAction = function (array $headers, array $body) use (&$called) {
            $called = true;
        };

        $events = Stub::make(Events::class, ['triggerAction' => $triggerAction]);
        $config = Stub::makeEmpty(ConfigInterface::class, ['getSettings' => $settings, 'getEvents' => $events]);
        $queue = new SqsQueue($config);
        $queue->listen();
        $I->assertFalse($called);
    }
}
