<?php

namespace MVF\Servicer;

use function Functional\map;
use OpenTracing\GlobalTracer;

trait EventHeaders
{
    use EventPayload;

    private $event;
    private $version;
    private $carrier;
    private $createdAt = 0;

    /**
     * Constructs array of object's attributes and values and transforms attributes to snake case.
     *
     * @return array
     */
    public function toPayload(): array
    {
        $attributes = get_object_vars($this);
        unset($attributes['createdAt']);

        $keys = map(array_keys($attributes), $this->transformToSnakeCase());
        $values = map(array_values($attributes), $this->transformToPayload());

        return array_combine($keys, $values);
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

        $span = GlobalTracer::get()->getActiveSpan();
        if (isset($span)) {
            GlobalTracer::get()->inject($span->getContext(), 'text_map', $carrier);
            $this->carrier = \GuzzleHttp\json_encode($carrier);
        } else {
            $this->carrier = null;
        }
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
