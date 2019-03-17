<?php

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

    /**
     * Gets the headers from the message.
     *
     * @param array $message Attributes of the message
     *
     * @return \stdClass
     */
    public function getHeaders(array $message): \stdClass
    {
        if (isset($message['MessageAttributes']) === true) {
            $messageAttributes = $message['MessageAttributes'];
            $keys = map($messageAttributes, $this->attributesToLowercase());
            $values = map($messageAttributes, $this->attributesToValues());
            $json = json_encode(array_combine($keys, $values));

            return json_decode($json);
        }

        return (object)[];
    }

    /**
     * Gets the body of the message.
     *
     * @param array $message Attributes of the message
     *
     * @return \stdClass
     */
    public function getBody(array $message): \stdClass
    {
        $body = (object)[];
        if (isset($message['Body']) === true) {
            $body = json_decode($message['Body']);
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
