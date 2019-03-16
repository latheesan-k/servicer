<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 16/03/19
 * Time: 20:43.
 */

namespace MVF\Servicer\Queues\PayloadParsers;

use function Functional\map;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class SqsSnsPayloadParser extends SqsStandardPayloadParser
{
    public function getHeaders(array $message): \stdClass
    {
        if (isset($message['Body']['MessageAttributes'])) {
            $messageAttributes = $message['Body']['MessageAttributes'];
            $keys = map($messageAttributes, $this->attributesToLowercase());
            $values = map($messageAttributes, $this->attributesToValues());
            $json = json_encode(array_combine($keys, $values));

            return json_decode($json);
        }

        return (object)[];
    }

    public function getBody(array $message): \stdClass
    {
        $body = (object)[];
        if (isset($message['Body']['Message']) === true) {
            $body = json_decode($message['Body']['Message']);
        }

        return $body;
    }

    private function attributesToValues(): callable
    {
        return function ($value) {
            return $value['Value'];
        };
    }
}
