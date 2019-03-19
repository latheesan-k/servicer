<?php

namespace MVF\Servicer\Queues\PayloadParsers;

use function Functional\map;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class SqsSnsPayloadParser extends SqsStandardPayloadParser
{
    /**
     * Gets the headers from the message.
     *
     * @param array $message Attributes of the message
     *
     * @return \stdClass
     */
    public function getHeaders(array $message): \stdClass
    {
        $body = \GuzzleHttp\json_decode($message['Body'], true);
        if (isset($body['MessageAttributes']) === true) {
            $messageAttributes = $body['MessageAttributes'];
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
        $body = json_decode($message['Body']);
        if (isset($body->{'Message'}) === true) {
            return json_decode($body->{'Message'});
        }

        return (object)[];
    }

    /**
     * Higher order function to convert message attributes to standard key value pair array.
     *
     * @return callable
     */
    private function attributesToValues(): callable
    {
        return function ($value) {
            return $value['Value'];
        };
    }
}
