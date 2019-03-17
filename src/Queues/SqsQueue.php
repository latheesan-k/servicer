<?php

namespace MVF\Servicer\Queues;

use MVF\Servicer\Clients\SqsClient;
use MVF\Servicer\ConfigInterface;
use MVF\Servicer\Events;
use MVF\Servicer\QueueInterface;
use MVF\Servicer\Queues\PayloadParsers\SqsSnsPayloadParser;
use MVF\Servicer\Queues\PayloadParsers\SqsStandardPayloadParser;
use MVF\Servicer\SettingsInterface;
use function Functional\each;

class SqsQueue implements QueueInterface
{
    /**
     * Defines events of the queue.
     *
     * @var Events
     */
    private $events;
    /**
     * Defines settings of the queue.
     *
     * @var SettingsInterface
     */
    private $settings;
    /**
     * Defines the debug function.
     *
     * @var callable
     */
    private $debug;

    /**
     * SqsQueue constructor.
     *
     * @param ConfigInterface $config Holds the queue configuration
     */
    public function __construct(ConfigInterface $config)
    {
        $this->settings = $config->getSettings();
        $this->events = $config->getEvents();
        $this->debug = function () {
        };
    }

    /**
     * Listen to the queue.
     */
    public function listen(): void
    {
        if ($this->settings->isCircuitBreakerClosed() === true) {
            ($this->debug)('DEBUG: circuit breaker is closed, no messages will be processed.');

            return;
        }

        each($this->receiveMessages(), $this->parseAndConsumeMessage());
    }

    /**
     * Sets the debug function.
     *
     * @param callable $debug Function that logs a debug message
     */
    public function setDebugFunction(callable $debug): void
    {
        $this->debug = $debug;
    }

    /**
     * Higher order function that parses and consumes the message received.
     *
     * @return callable
     */
    private function parseAndConsumeMessage(): callable
    {
        return function ($message) {
            ($this->debug)('DEBUG: parsing message payload');
            $parser = $this->getPayloadParser($message);
            $headers = $parser->getHeaders($message);
            $body = $parser->getBody($message);

            ($this->debug)('DEBUG: checking if message is old');
            $consumeMessage = $this->consumeMessage($headers, $body, $message);
            $this->settings->isOldMessage($headers, $consumeMessage);
        };
    }

    /**
     * Gets the right kind of payload parser.
     *
     * @param array $message Attributes of the message
     *
     * @return SqsSnsPayloadParser|SqsStandardPayloadParser
     */
    private function getPayloadParser(array $message)
    {
        if (isset($message['Body']['Type']) === true && $message['Body']['Type'] === 'Notification') {
            return new SqsSnsPayloadParser();
        }

        return new SqsStandardPayloadParser();
    }

    /**
     * Higher order function that consumes the message received.
     *
     * @param \stdClass $headers Header attributes of the message
     * @param \stdClass $body    Body attributes of the message
     * @param array     $message Attributes of the received message
     *
     * @return callable
     */
    private function consumeMessage(\stdClass $headers, \stdClass $body, array $message): callable
    {
        return function () use ($headers, $body, $message) {
            ($this->debug)('DEBUG: consuming message');
            $this->events->triggerAction($headers, $body);
            $this->deleteMessage($message['ReceiptHandle']);
            ($this->debug)('DEBUG: message consumed successfully');
        };
    }

    /**
     * Receives messages from sqs queue.
     *
     * @return array
     */
    private function receiveMessages(): array
    {
        ($this->debug)('DEBUG: receiving message from SQS');
        $result = SqsClient::instance()->receiveMessage(
            [
                'AttributeNames' => ['SentTimestamp'],
                'MaxNumberOfMessages' => 1,
                'MessageAttributeNames' => ['All'],
                'QueueUrl' => $this->getSqsUrl(),
                'WaitTimeSeconds' => 0,
            ]
        );

        $messages = $result->get('Messages');
        if (isset($messages) === true) {
            return $messages;
        }

        ($this->debug)('DEBUG: no message receiving from SQS');

        return [];
    }

    /**
     * Deletes messages from sqs queue.
     *
     * @param string $receipt The id of the message to be deleted
     */
    private function deleteMessage(string $receipt)
    {
        SqsClient::instance()->deleteMessage(
            [
                'QueueUrl' => $this->getSqsUrl(),
                'ReceiptHandle' => $receipt,
            ]
        );
    }

    /**
     * Get the sqs queue url.
     *
     * @return string
     */
    private function getSqsUrl(): string
    {
        return getenv('SQS_URL') . $this->settings->getName();
    }
}
