<?php

namespace MVF\Servicer\Services;

use DDTrace\Format;
use OpenTracing\GlobalTracer;
use OpenTracing\Span;
use function GuzzleHttp\json_decode;
use function GuzzleHttp\json_encode;

class TracerCapsule
{
    /**
     * Injects the span into the carrier.
     *
     * @return string
     */
    public static function injectCarrier(): string
    {
        $trace = GlobalTracer::get();
        $span = $trace->getActiveSpan();
        if (isset($span) === false) {
            return '';
        }

        $trace->inject($span->getContext(), Format::TEXT_MAP, $carrier);

        if (self::isValidCarrier($carrier) === true) {
            return base64_encode(json_encode($carrier));
        }

        return '';
    }

    /**
     * Extracts the span from the carrier.
     *
     * @param string      $name    The name of the operation
     * @param null|string $carrier The carrier for the span
     *
     * @return Span
     */
    public static function extractCarrier(string $name, ?string $carrier): Span
    {
        $tracer = GlobalTracer::get();
        $span = $tracer->startSpan($name);

        $carrier = self::decodeCarrier($carrier);
        if (self::isValidCarrier($carrier) === true) {
            $context = $tracer->extract(Format::TEXT_MAP, $carrier);
            $span = $tracer->startSpan($name, ['child_of' => $context->unwrapped()]);
        }

        $tracer->getScopeManager()->activate($span);

        return $span;
    }

    /**
     * Decodes the carrier.
     *
     * @param null|string $json The carrier json
     *
     * @return array
     */
    public static function decodeCarrier(?string $json): array
    {
        $carrier = [];

        try {
            $carrier = json_decode(base64_decode($json), true);
        } catch (\Exception $exception) {
            $message = [
                'message' => "Unable to parse carrier '"
                    . ($json ?? 'null')
                    . "' exception thrown "
                    . $exception->getMessage(),
                'severity' => 'WARNING',
            ];

            echo json_encode($message) . PHP_EOL;
        }

        return $carrier;
    }

    /**
     * Checks if the carrier is valid.
     *
     * @param array|null $carrier The carrier associative array
     *
     * @return bool
     */
    public static function isValidCarrier(?array $carrier): bool
    {
        return isset(
            $carrier['x-datadog-trace-id'],
            $carrier['x-datadog-parent-id'],
            $carrier['x-datadog-sampling-priority']
        );
    }
}
