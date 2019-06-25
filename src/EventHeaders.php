<?php

namespace MVF\Servicer;

use OpenTracing\GlobalTracer;
use function Functional\map;
use function GuzzleHttp\json_encode;

trait EventHeaders
{
    use EventPayload;

    private $event;
    private $version;
    private $createdAt = 0;
    private $carrier = null;

    /**
     * Constructs array of object's attributes and values and transforms attributes to snake case.
     *
     * @return array
     */
    public function toPayload(): array
    {
        $this->injectCarrier();

        $attributes = get_object_vars($this);
        unset($attributes['createdAt']);

        $carrier = ($attributes['carrier'] ?? null);
        if ($this->isInvalidCarrier($carrier)) {
            unset($attributes['carrier']);
        } else {
            $attributes['carrier'] = json_encode($attributes['carrier']);
        }

        $keys = map(array_keys($attributes), $this->transformToSnakeCase());
        $values = map(array_values($attributes), $this->transformToPayload());

        return array_combine($keys, $values);
    }

    private function injectCarrier()
    {
        $tracer = GlobalTracer::get();
        $span = $tracer->getActiveSpan();
        if (isset($span)) {
            $context = $span->getContext();
            $tracer->inject($context, 'text_map', $this->carrier);
        }
    }

    /**
     * Checks if a valid carrier is provided.
     *
     * @param string[]|null $carrier In the payload header
     *
     * @return bool
     */
    private function isInvalidCarrier(?array $carrier): bool
    {
        return !isset(
            $carrier['x-datadog-trace-id'],
            $carrier['x-datadog-parent-id'],
            $carrier['x-datadog-sampling-priority']
        );
    }

    /**
     * Get the name of the event.
     *
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * Get the version of the event.
     *
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Get the unix timestamp this event was created at.
     *
     * @return int
     */
    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    /**
     * Initialises header attributes.
     *
     * @param string $event   Event name
     * @param string $version Event version
     */
    private function initialise(string $event, string $version)
    {
        $this->event = $event;
        $this->version = $version;
    }

    /**
     * Sets header attributes using provided array.
     *
     * @param array $headers Array with expected header attributes
     */
    private function loadRemaining(array $headers)
    {
        $this->event = $headers['event'];
        $this->createdAt = $headers['created_at'];
    }
}
