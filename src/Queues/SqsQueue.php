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
use MVF\Servicer\EventsInterface;
use MVF\Servicer\QueueInterface;
use MVF\Servicer\Queues\PayloadParsers\SqsSnsPayloadParser;
use MVF\Servicer\Queues\PayloadParsers\SqsStandardPayloadParser;
use MVF\Servicer\SettingsInterface;
use function Functional\each;

class SqsQueue implements QueueInterface
{
    /**
     * @var EventsInterface
     */
    private $events;
    /**
     * @var SettingsInterface
     */
    private $settings;

    public function __construct(ConfigInterface $config)
    {
        $this->settings = $config->getSettings();
        $this->events = $config->getEvents();
    }

    public function listen(): void
    {
        if ($this->settings->isCircuitBreakerClosed()) {
            return;
        }

        each($this->receiveMessages(), $this->handleMessages());
    }

    private function handleMessages(): callable
    {
        return function ($message) {
            $parser = new SqsStandardPayloadParser();
            if (isset($message["Body"]["Type"]) && $message["Body"]["Type"] === "Notification") {
                $parser = new SqsSnsPayloadParser();
            }

            $headers = $parser->getHeaders($message);
            $body = $parser->getBody($message);
            $this->events->triggerAction($headers, $body);
            $this->deleteMessage($message['ReceiptHandle']);
        };
    }

    private function receiveMessages(): array
    {
        $result = SqsClient::instance()->receiveMessage(
            [
                'AttributeNames'        => ['SentTimestamp'],
                'MaxNumberOfMessages'   => 1,
                'MessageAttributeNames' => ['All'],
                'QueueUrl'              => $this->getSqsUrl(),
                'WaitTimeSeconds'       => 0,
            ]
        );

        $messages = $result->get('Messages');
        if (isset($messages)) {
            return $messages;
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
}
