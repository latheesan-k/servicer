<?php

namespace MVF\Servicer;

use OpenTracing\GlobalTracer;
use OpenTracing\Span;
use ReflectionClass;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

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
            $span = self::getSpan($reflect, $carrier);
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

    /**
     * Extract span from carrier or create a new one.
     *
     * @param ReflectionClass $reflect The class properties of the action
     * @param string|null     $carrier In the payload header
     *
     * @return Span
     */
    private static function getSpan(ReflectionClass $reflect, ?string $carrier): Span
    {
        $tracer = GlobalTracer::get();
        $scope = $tracer->startActiveSpan($reflect->getShortName());

        if (self::isValidCarrier($carrier) === true) {
            $context = $tracer->extract('text_map', $carrier);
            $scope = $tracer->startActiveSpan(
                $reflect->getShortName(),
                ['child_of' => $context->unwrapped()]
            );
        }

        return $scope->getSpan();
    }

    /**
     * Checks if a valid carrier is provided.
     *
     * @param string|null $carrier In the payload header
     *
     * @return bool
     */
    private static function isValidCarrier(?string $carrier): bool
    {
        try {
            $carrier = json_decode($carrier, true);

            return isset(
                $carrier['x-datadog-trace-id'],
                $carrier['x-datadog-parent-id'],
                $carrier['x-datadog-sampling-priority']
            );
        } catch (\Exception $exception) {
            $message = [
                'message' => "Unable to parse carrier '"
                    . ($carrier ?? 'null')
                    . "' exception thrown "
                    . $exception->getMessage(),
                'severity' => 'ERROR',
            ];

            echo json_encode($message) . PHP_EOL;

            return false;
        }
    }
}
