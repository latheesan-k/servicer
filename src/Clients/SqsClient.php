<?php

namespace MVF\Servicer\Clients;

use Aws\Credentials\Credentials;
use Aws\Result;
use Aws\Sqs\SqsClient as Client;

class SqsClient
{
    private static $instance;
    private $client;

    /**
     * SqsClient constructor.
     */
    private function __construct()
    {
        $this->client = new Client(
            [
                'region' => getenv('AWS_REGION'),
                'version' => getenv('SQS_VERSION'),
                'credentials' => new Credentials(
                    getenv('AWS_ACCESS_KEY_ID'),
                    getenv('AWS_SECRET_ACCESS_KEY')
                ),
            ]
        );
    }

    /**
     * Receive message from the queue.
     *
     * @param array $args Sqs receive attributes
     *
     * @return Result
     */
    public function receiveMessage(array $args = []): Result
    {
        return $this->client->receiveMessage($args);
    }

    /**
     * Delete message from the queue.
     *
     * @param array $args Sqs delete attributes
     *
     * @return Result
     */
    public function deleteMessage(array $args = []): Result
    {
        return $this->client->deleteMessage($args);
    }

    /**
     * Singleton to get the sqs client.
     *
     * @return SqsClient
     */
    public static function instance(): SqsClient
    {
        if (empty(self::$instance) === true) {
            self::$instance = new SqsClient();
        }

        return self::$instance;
    }
}
