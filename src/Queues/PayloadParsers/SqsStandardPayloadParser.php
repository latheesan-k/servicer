<?php

namespace MVF\Servicer\Queues\PayloadParsers;

use function Functional\map;
use function GuzzleHttp\json_decode;

class SqsStandardPayloadParser
{
    const TYPES = [
        'String' => 'StringValue',
        'Number' => 'StringValue',
        'Binary' => 'BinaryValue',
    ];

    /**
     * Gets the headers from the message.
     *
     * @param array $message Attributes of the message
     *
     * @return array
     */
    public function getHeaders(array $message): array
    {
        if (isset($message['MessageAttributes']) === true) {
            $messageAttributes = $message['MessageAttributes'];
            $keys = map($messageAttributes, $this->attributesToLowercase());
            $values = map($messageAttributes, $this->attributesToValues());

            return array_combine($keys, $values);
        }

        return [];
    }

    /**
     * Gets the body of the message.
     *
     * @param array $message Attributes of the message
     *
     * @return array
     */
    public function getBody(array $message): array
    {
        $body = [];
        if (isset($message['Body']) === true) {
            $body = json_decode($message['Body'], true);
        }

        return $body;
    }

    /**
     * Higher order function to convert keys to lower case.
     *
     * @return callable
     */
    protected function attributesToLowercase(): callable
    {
        return function ($value, $key) {
            return strtolower($key);
        };
    }

    /**
     * Higher order function to convert message attributes to standard key value pair array.
     *
     * @return callable
     */
    private function attributesToValues(): callable
    {
        return function ($value) {
            return $value[self::TYPES[$value['DataType']]];
        };
    }
}
