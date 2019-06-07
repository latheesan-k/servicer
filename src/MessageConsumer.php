<?php
/**
 * Created by PhpStorm.
 * User: eleanorwintram
 * Date: 07/06/2019
 * Time: 17:41
 */

namespace MVF\Servicer;


use OpenTracing\GlobalTracer;
use ReflectionClass;

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

            if (isset($headers['carrier']) === true) {
                GlobalTracer::get()->extract('text_map', $headers['carrier']);
            }

            $span = GlobalTracer::get()->startActiveSpan($reflect->getShortName())->getSpan();
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
        $payload = [
            'severity' => $severity,
            'event' => ($headers['event'] ?? $action),
            'action' => $action,
            'state' => $state,
            'message' => 'Payload: ' . json_encode(['headers' => $headers, 'body' => $body]),
        ];

        echo json_encode($payload) . PHP_EOL;
    }
}