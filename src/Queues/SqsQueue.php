<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 24/12/18
 * Time: 00:51.
 */

namespace MVF\Servicer\Consumer\Queues;

use Aws\Acm\Exception\AcmException;
use MVF\Servicer\Consumer\Clients\SqsClient;
use MVF\Servicer\Consumer\ConfigInterface;
use MVF\Servicer\Consumer\EventInterface;
use MVF\Servicer\Consumer\Exceptions\NoMessagesException;
use MVF\Servicer\Consumer\QueueInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use function Functional\each;

class SqsQueue extends ConsoleOutput implements QueueInterface
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
        parent::__construct();
    }

    public function listen(): void
    {
        if ($this->config->skip()) {
            return;
        }

        try {
            each($this->receiveMessages(), $this->handleMessages());
        } catch (AcmException $exception) {
            $this->writeln($exception->getMessage());
        } catch (NoMessagesException $exception) {
            // Do nothing
        } catch (\Exception $exception) {
            $this->writeln($exception->getMessage());
        }
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

        $messages = $result->get('Messages');
        if (empty($messages) === true) {
            throw new NoMessagesException();
        }

        return $messages;
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
        $messageAttributes = $message['MessageAttributes'];
        if (empty($messageAttributes) === false) {
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
