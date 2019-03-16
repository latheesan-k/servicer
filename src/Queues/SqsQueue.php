<?php

namespace MVF\Servicer\Queues;

use MVF\Servicer\Clients\SqsClient;
use MVF\Servicer\ConfigInterface;
use MVF\Servicer\Events;
use MVF\Servicer\QueueInterface;
use MVF\Servicer\Queues\PayloadParsers\SqsSnsPayloadParser;
use MVF\Servicer\Queues\PayloadParsers\SqsStandardPayloadParser;
use MVF\Servicer\SettingsInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;
use function Functional\each;

class SqsQueue implements QueueInterface
{
    /**
     * @var Events
     */
    private $events;
    /**
     * @var SettingsInterface
     */
    private $settings;
    /**
     * @var StreamOutput
     */
    private $output;

    public function __construct(ConfigInterface $config)
    {
        $this->settings = $config->getSettings();
        $this->events = $config->getEvents();
        $this->output = new ConsoleOutput();
    }

    public function listen(): void
    {
        if ($this->settings->isCircuitBreakerClosed()) {
            if ($this->output->isDebug()) {
                $this->output->writeln('DEBUG: circuit breaker is closed, no messages will be processed.');
            }

            return;
        }

        each($this->receiveMessages(), $this->parsePayload());
    }

    public function setConsoleOutput(StreamOutput $output): void
    {
        $this->output = $output;
    }

    private function parsePayload(): callable
    {
        return function ($message) {
            if ($this->output->isDebug()) {
                $this->output->writeln('DEBUG: parsing message payload');
            }

            $parser = new SqsStandardPayloadParser();
            if (isset($message['Body']['Type']) && $message['Body']['Type'] === 'Notification') {
                $parser = new SqsSnsPayloadParser();
            }

            $headers = $parser->getHeaders($message);
            $body = $parser->getBody($message);

            if ($this->output->isDebug()) {
                $this->output->writeln('DEBUG: checking if message is old');
            }

            $consumeMessage = $this->consumeMessage($headers, $body, $message);
            $this->settings->isOldMessage($headers, $consumeMessage);
        };
    }

    private function consumeMessage(\stdClass $headers, \stdClass $body, $message): callable
    {
        return function () use ($headers, $body, $message) {
            if ($this->output->isDebug()) {
                $this->output->writeln('DEBUG: consuming message');
            }

            $this->events->triggerAction($headers, $body);
            $this->deleteMessage($message['ReceiptHandle']);

            if ($this->output->isDebug()) {
                $this->output->writeln('DEBUG: message consumed successfully');
            }
        };
    }

    private function receiveMessages(): array
    {
        if ($this->output->isDebug()) {
            $this->output->writeln('DEBUG: receiving message from SQS');
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

        $messages = $result->get('Messages');
        if (isset($messages)) {
            return $messages;
        }

        if ($this->output->isDebug()) {
            $this->output->writeln('DEBUG: no message receiving from SQS');
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
