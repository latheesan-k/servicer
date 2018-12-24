<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 24/12/18
 * Time: 00:51
 */

namespace MVF\Servicer\Queues;

use Aws\Acm\Exception\AcmException;
use Aws\Credentials\Credentials;
use Aws\Sqs\SqsClient;
use MVF\Servicer\ActionsInterface;
use MVF\Servicer\ErrorInterface;
use MVF\Servicer\QueueInterface;

class SqsQueue implements QueueInterface
{
    const QUEUE = 'queue';
    const TYPES = [
        'String' => 'StringValue',
        'Number' => 'StringValue',
        'Binary' => 'BinaryValue',
    ];
    /**
     * @var string
     */
    private $queue;

    public function __construct(string $queue)
    {
        $this->queue = $queue;
    }

    function listen(ActionsInterface $actions, ErrorInterface $error): void
    {
        $client = new SqsClient(
            [
                'region'      => getenv('AWS_REGION'),
                'version'     => getenv('SQS_VERSION'),
                'credentials' => new Credentials(
                    getenv('AWS_ACCESS_KEY_ID'),
                    getenv('AWS_SECRET_ACCESS_KEY')
                ),
            ]
        );

        while (true) {
            try {
                $queue = getenv('SQS_URL') . $this->queue;
                $result = $client->receiveMessage(
                    [
                        'AttributeNames'        => ['SentTimestamp'],
                        'MaxNumberOfMessages'   => 1,
                        'MessageAttributeNames' => ['All'],
                        'QueueUrl'              => $queue,
                        'WaitTimeSeconds'       => 0,
                    ]
                );

                $messages = $result->get('Messages');
                if (empty($messages) === true) {
                    continue;
                }

                foreach ($messages as $message) {
                    $messageAttributes = $message['MessageAttributes'];

                    $headers = [];
                    if (empty($messageAttributes) === false) {
                        foreach ($messageAttributes as $attribute => $payload) {
                            $type = $payload['DataType'];
                            $headers[$attribute] = $payload[self::TYPES[$type]];
                        }
                    }

                    $body = '';
                    if (isset($message['Body']) === true) {
                        $body = $message['Body'];
                    }

                    $action = $actions->getAction($headers['Action']);
                    $action->handle($headers, $body);

                    $client->deleteMessage(
                        [
                            'QueueUrl'      => $queue,
                            'ReceiptHandle' => $message['ReceiptHandle'],
                        ]
                    );
                }
            } catch (AcmException $exception) {
                $error->handleException($exception);
            }

            usleep(10);
        }
    }
}