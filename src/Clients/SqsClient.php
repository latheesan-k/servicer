<?php

namespace MVF\Servicer\Clients;

use Aws\Credentials\Credentials;
use Aws\Result;
use Aws\Sqs\SqsClient as Client;

class SqsClient
{
    private static $instance;
    private $client;

    private function __construct()
    {
        $this->client = new Client(
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

    public function receiveMessage(array $args = []): Result
    {
        return $this->client->receiveMessage($args);
    }

    public function deleteMessage(array $args = []): Result
    {
        return $this->client->deleteMessage($args);
    }

    public static function instance(): SqsClient
    {
        if (empty(self::$instance)) {
            self::$instance = new SqsClient();
        }

        return self::$instance;
    }
}
