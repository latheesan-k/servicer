<?php

namespace MVF\Servicer;

interface ActionInterface
{
    /**
     * Determines if message should be consumed.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body    Payload of the event
     * @param callable  $consume Callback function that should be called if message should be consumed
     */
    public function beforeAction(\stdClass $headers, \stdClass $body, callable $consume): void;

    /**
     * Executes the action.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body    Body of the event
     */
    public function handle(\stdClass $headers, \stdClass $body): void;
}
