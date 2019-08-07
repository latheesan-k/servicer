<?php

namespace MVF\Servicer;

use MVF\Servicer\Services\LogCapsule;
use MVF\Servicer\Services\TracerCapsule;
use ReflectionClass;
use function Functional\invoke;

class MessageConsumer
{
    /**
     * Higher order function that consumes the message.
     *
     * @param ActionInterface $action  Action to be executed
     * @param array           $headers Attributes of the message headers
     * @param array           $body    Attributes of the message body
     *
     * @return callable
     */
    public static function consume(ActionInterface $action, array $headers, array $body): callable
    {
        return function () use ($action, $headers, $body) {
            $reflect = new ReflectionClass($action);
            $carrier = ($headers['carrier'] ?? null);
            $span = TracerCapsule::extractCarrier($reflect->getShortName(), $carrier);
            self::log('INFO', $reflect->getShortName(), 'STARTED', $headers, $body);
            $action->handle($headers, $body);
            self::log('INFO', $reflect->getShortName(), 'COMPLETED', $headers, $body);
            $span->finish();
        };
    }

    /**
     * Logs whether the event was handled.
     *
     * @param string $severity The severity of the message
     * @param string $action   The action being logged
     * @param string $state    The state of the event
     * @param array  $headers  Attributes of the message headers
     * @param array  $body     Attributes of the message body
     */
    public static function log(string $severity, string $action, string $state, array $headers, array $body): void
    {
        $message = 'Payload: ' . \GuzzleHttp\json_encode(['headers' => $headers, 'body' => $body]);
        $logger = new LogCapsule(['action' => $action, 'state' => $state, 'event' => $headers['event']]);
        invoke([$logger], $severity, [$message]);
    }
}
