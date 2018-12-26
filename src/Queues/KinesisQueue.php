<?php

namespace Src;

use Aws\Credentials\Credentials;
use Aws\Kinesis\KinesisClient;
use MVF\Servicer\ActionsInterface;
use MVF\Servicer\ErrorInterface;
use MVF\Servicer\QueueInterface;

class KinesisQueue implements QueueInterface
{
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
        $client = new KinesisClient([
            'region'      => getenv('AWS_REGION'),
            'version'     => getenv('SQS_VERSION'),
            'credentials' => new Credentials(
                getenv('AWS_ACCESS_KEY_ID'),
                getenv('AWS_SECRET_ACCESS_KEY')
            ),
        ]);

        $result = $client->getShardIterator([
            'StreamName' => 'temp',
            'ShardId' => 'shardId-000000000000',
            'ShardIteratorType' => 'LATEST'
        ]);

        $shardIterator = $result['ShardIterator'];

        while (true) {
            $result = $client->getRecords(['ShardIterator' => $shardIterator]);
            foreach ($result['Records'] as $record) {
                $data = \GuzzleHttp\json_decode($record['Data']);
                $headers = $data['Headers'];
                $action = $actions->getAction($headers->action);
                $action->handle($headers, $data['Body']);
            }
            $shardIterator = $result['NextShardIterator'];
            usleep(10);
        }
    }
}