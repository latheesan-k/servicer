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
use MVF\Servicer\QueueInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SqsQueue implements QueueInterface
{
    const QUEUE = 'queue';
    const TYPES = [
        'String' => 'StringValue',
        'Number' => 'StringValue',
        'Binary' => 'BinaryValue',
    ];

    private $client;

    public function __construct()
    {
        $this->client = new SqsClient(
            [
                'region'      => getenv('AWS_REGION'),
                'version'     => getenv('SQS_VERSION'),
                'credentials' => new Credentials(
                    getenv('AWS_ACCESS_KEY_ID'),
                    getenv('AWS_SECRET_ACCESS_KEY')
                ),
            ]
        );
    }

    function listen(ActionsInterface $actions, InputInterface $input, OutputInterface $output): void
    {
        while (true) {
            try {
                $queue = getenv('SQS_URL') . $input->getArgument(self::QUEUE);
                $result = $this->client->receiveMessage(
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
                    $action = $actions->getAction($messageAttributes['Action']['StringValue']);

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

                    $action->handle($headers, $body);
                    $this->client->deleteMessage(
                        [
                            'QueueUrl'      => $queue,
                            'ReceiptHandle' => $message['ReceiptHandle'],
                        ]
                    );
                }
            } catch (AcmException $e) {
                $output->writeln($e->getMessage());
            }

            usleep(10);
        }
    }
}