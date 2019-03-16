<?php

namespace MVF\Servicer;

interface ActionInterface
{
    /**
     * Determines if message should be consumed.
     *
     * @param \stdClass $headers Headers of the event
     * @param callable  $consumeMessage Callback function that should be called if message should be consumed
     */
    public function skipMessage(\stdClass $headers, callable $consumeMessage): void;

    /**
     * Executes the action.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body    Body of the event
     */
    public function handle(\stdClass $headers, \stdClass $body): void;
}
