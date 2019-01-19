<?php

namespace MVF\Servicer\Clients;

use Aws\Credentials\Credentials;
use Aws\Sqs\SqsClient as Client;

class SqsClient
{
    private static $client;

    public static function instance(): Client
    {
        if (empty($client)) {
            self::$client = new Client(
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

        return self::$client;
    }
}
