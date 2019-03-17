<?php

namespace MVF\Servicer;

interface SettingsInterface
{
    /**
     * Gets the name of the queue.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Check if circuit breaker condition is triggered.
     *
     * @return bool
     */
    public function isCircuitBreakerClosed(): bool;

    /**
     * Determines if message should be consumed.
     *
     * @param \stdClass $headers        Attributes of the message headers
     * @param callable  $consumeMessage Callback function that triggers the consumption of the message
     */
    public function isOldMessage(\stdClass $headers, callable $consumeMessage): void;
}
