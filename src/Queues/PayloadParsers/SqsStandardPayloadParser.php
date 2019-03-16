<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 16/03/19
 * Time: 20:30.
 */

namespace MVF\Servicer\Queues\PayloadParsers;

use function Functional\map;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class SqsStandardPayloadParser
{
    const TYPES = [
        'String' => 'StringValue',
        'Number' => 'StringValue',
        'Binary' => 'BinaryValue',
    ];

    public function getHeaders(array $message): \stdClass
    {
        if (isset($message['MessageAttributes'])) {
            $messageAttributes = $message['MessageAttributes'];
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
        if (isset($message['Body']) === true) {
            $body = json_decode($message['Body']);
        }

        return $body;
    }

    protected function attributesToLowercase(): callable
    {
        return function ($value, $key) {
            return strtolower($key);
        };
    }

    private function attributesToValues(): callable
    {
        return function ($value) {
            return $value[self::TYPES[$value['DataType']]];
        };
    }
}
