<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 24/12/18
 * Time: 00:51.
 */

namespace MVF\Servicer\Queues;

use Aws\Acm\Exception\AcmException;
use MVF\Servicer\Clients\SqsClient;
use MVF\Servicer\ConfigInterface;
use MVF\Servicer\EventInterface;
use MVF\Servicer\Exceptions\NoMessagesException;
use MVF\Servicer\QueueInterface;
use PHPUnit\Runner\Exception;
use Symfony\Component\Console\Output\ConsoleOutput;
use function Functional\each;

class SqsQueue implements QueueInterface
{
    const TYPES = [
        'String' => 'StringValue',
        'Number' => 'StringValue',
        'Binary' => 'BinaryValue',
    ];

    /**
     * @var EventInterface
     */
    private $events;
    /**
     * @var ConfigInterface
     */
    private $config;

    public function __construct(ConfigInterface $config, EventInterface $events)
    {
        $this->config = $config;
        $this->events = $events;
    }

    public function listen(): void
    {
        if ($this->config->isCircuitBreakerClosed()) {
            return;
        }

        each($this->receiveMessages(), $this->handleMessages());
    }

    public function getEvents(): EventInterface
    {
        return $this->events;
    }

    private function handleMessages(): callable
    {
        return function ($message) {
            $headers = $this->getMessageHeaders($message);
            $body = $this->getMessageBody($message);
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

        $message = $result->get('Messages');
        if (isset($message)) {
            return $message;
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
        return getenv('SQS_URL') . $this->config->getName();
    }

    private function getMessageHeaders(array $message): \stdClass
    {
        $headers = (object)[];
        if (isset($message['MessageAttributes'])) {
            $messageAttributes = $message['MessageAttributes'];
            foreach ($messageAttributes as $attribute => $payload) {
                $type = $payload['DataType'];
                $field = strtolower($attribute);
                $headers->$field = $payload[self::TYPES[$type]];
            }
        }

        return $headers;
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
