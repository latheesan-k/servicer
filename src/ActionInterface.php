<?php

namespace MVF\Servicer;

interface ActionInterface
{
    /**
     * Test if message is old.
     *
     * @param int       $timestamp
     * @param \stdClass $headers
     * @param callable  $consumeMessage
     */
    public function isOldMessage(int $timestamp, \stdClass $headers, callable $consumeMessage): void;

    /**
     * Executes the action.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body    Body of the event
     */
    public function handle(\stdClass $headers, \stdClass $body): void;
}
