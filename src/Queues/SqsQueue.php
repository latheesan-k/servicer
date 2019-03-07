<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 24/12/18
 * Time: 00:51.
 */

namespace MVF\Servicer\Queues;

use MVF\Servicer\Clients\SqsClient;
use MVF\Servicer\ConfigInterface;
use MVF\Servicer\Events;
use MVF\Servicer\QueueInterface;
use MVF\Servicer\SettingsInterface;
use function Functional\each;
use function Functional\map;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;
use Symfony\Component\Console\Output\ConsoleOutput;

class SqsQueue implements QueueInterface
{
    const TYPES = [
        'String' => 'StringValue',
        'Number' => 'StringValue',
        'Binary' => 'BinaryValue',
    ];

    /**
     * @var Events
     */
    private $events;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var ConsoleOutput
     */
    private $output;

    public function __construct(ConfigInterface $config)
    {
        $this->settings = $config->getSettings();
        $this->events = $config->getEvents();
    }

    public function listen(): void
    {
        if ($this->settings->isCircuitBreakerClosed()) {
            if ($this->output->isDebug()) {
                $this->output->writeln("DEBUG: circuit breaker is closed, no messages will be processed.");
            }

            return;
        }

        each($this->receiveMessages(), $this->parsePayload());
    }

    public function setConsoleOutput(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    private function parsePayload(): callable
    {
        return function ($message) {
            if ($this->output->isDebug()) {
                $this->output->writeln("DEBUG: parsing message payload");
            }

            $headers = $this->getMessageHeaders($message);
            $body = $this->getMessageBody($message);
            $timestamp = $message['Attributes']['SentTimestamp'];

            if ($this->output->isDebug()) {
                $this->output->writeln("DEBUG: checking if message is old");
            }

            $consumeMessage = $this->consumeMessage($headers, $body, $message);
            $this->settings->isOldMessage($timestamp, $headers, $consumeMessage);
        };
    }

    private function consumeMessage(\stdClass $headers, \stdClass $body, $message): callable
    {
        return function () use ($headers, $body, $message) {
            if ($this->output->isDebug()) {
                $this->output->writeln("DEBUG: consuming message");
            }

            $timestamp = $message['Attributes']['SentTimestamp'];
            $this->events->triggerAction($timestamp, $headers, $body);
            $this->deleteMessage($message['ReceiptHandle']);

            if ($this->output->isDebug()) {
                $this->output->writeln("DEBUG: message consumed successfully");
            }
        };
    }

    private function receiveMessages(): array
    {
        if ($this->output->isDebug()) {
            $this->output->writeln("DEBUG: receiving message from SQS");
        }

        $result = SqsClient::instance()->receiveMessage(
            [
                'AttributeNames'        => ['SentTimestamp'],
                'MaxNumberOfMessages'   => 1,
                'MessageAttributeNames' => ['All'],
                'QueueUrl'              => $this->getSqsUrl(),
                'WaitTimeSeconds'       => 0,
            ]
        );

        $message = $result->get('Messages');
        if (isset($message)) {
            return $message;
        }

        if ($this->output->isDebug()) {
            $this->output->writeln("DEBUG: no message receiving from SQS");
        }

        return [];
    }

    private function deleteMessage(string $receipt)
    {
        SqsClient::instance()->deleteMessage(
            [
                'QueueUrl'      => $this->getSqsUrl(),
                'ReceiptHandle' => $receipt,
            ]
        );
    }

    private function getSqsUrl(): string
    {
        return getenv('SQS_URL') . $this->settings->getName();
    }

    private function getMessageHeaders(array $message): \stdClass
    {
        if (isset($message['MessageAttributes'])) {
            $messageAttributes = $message['MessageAttributes'];
            $keys = map($messageAttributes, $this->attributesToLowercase());
            $values = map($messageAttributes, $this->attributesToValues());
            $json = json_encode(array_combine($keys, $values));

            return json_decode($json);
        }

        return (object)[];
    }

    private function attributesToLowercase(): callable
    {
        return function ($value, $key) {
            return strtolower($key);
        };
    }

    private function attributesToValues(): callable
    {
        return function ($value) {
            return $value[self::TYPES[$value['DataType']]];
        };
    }

    private function getMessageBody(array $message): \stdClass
    {
        $body = (object)[];
        if (isset($message['Body']) === true) {
            $body = \GuzzleHttp\json_decode($message['Body']);
        }

        return $body;
    }
}
