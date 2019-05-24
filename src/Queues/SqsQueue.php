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
     * SqsQueue constructor.
     *
     * @param ConfigInterface $config Holds the queue configuration
     */
    public function __construct(ConfigInterface $config)
    {
        $this->settings = $config->getSettings();
        $this->events = $config->getEvents();
    }

    /**
     * Listen to the queue.
     */
    public function listen(): void
    {
        $receive = function () {
            each($this->receiveMessages(), $this->parseAndConsumeMessage());
        };

        $this->settings->beforeReceive($receive);
    }

    /**
     * Higher order function that parses and consumes the message received.
     *
     * @return callable
     */
    private function parseAndConsumeMessage(): callable
    {
        return function ($message) {
            $parser = $this->getPayloadParser($message);
            $headers = $parser->getHeaders($message);
            $body = $parser->getBody($message);

            $this->events->triggerActions($headers, $body);
            $this->deleteMessage($message['ReceiptHandle']);
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
        if (isset($message['Body']) === true) {
            $body = \GuzzleHttp\json_decode($message['Body'], true);
            if (isset($body['Type']) === true && $body['Type'] === 'Notification') {
                return new SqsSnsPayloadParser();
            }
        }

        return new SqsStandardPayloadParser();
    }

    /**
     * Receives messages from sqs queue.
     *
     * @return array
     */
    private function receiveMessages(): array
    {
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
