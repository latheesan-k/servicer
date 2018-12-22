<?php

namespace MVF\Servicer\Commands;

use Aws\Acm\Exception\AcmException;
use Aws\Credentials\Credentials;
use Aws\Sqs\SqsClient;
use MVF\Servicer\BaseCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DaemonCommand extends BaseCommand
{
    const QUEUE = 'queue';
    const TYPES = [
        'String' => 'StringValue',
        'Number' => 'StringValue',
        'Binary' => 'BinaryValue',
    ];

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('daemon');
        $this->setDescription('Run blocking daemon that listens for actions');
        $this->setHelp('Not implemented');

        $this->addArgument(
            self::QUEUE,
            InputArgument::REQUIRED,
            'The queue to be listened'
        );
    }

    /**
     * Defines the behaviour of the command.
     *
     * @param InputInterface  $input  Defines inputs
     * @param OutputInterface $output Defines outputs
     */
    protected function execute(InputInterface $input, OutputInterface $output)
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
                $queue = getenv('SQS_URL') . $input->getArgument(self::QUEUE);
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
                    $action = $this->getAction($messageAttributes['Action']['StringValue']);

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
                    $client->deleteMessage(
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
