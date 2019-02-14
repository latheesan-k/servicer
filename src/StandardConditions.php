<?php

namespace MVF\Servicer;

abstract class StandardConditions implements ActionInterface
{
    /**
     * Test if message is old.
     *
     * @param int       $timestamp
     * @param \stdClass $headers
     * @param callable  $consumeMessage
     */
    public function isOldMessage(int $timestamp, \stdClass $headers, callable $consumeMessage): void
    {
        $consumeMessage();
    }
}
