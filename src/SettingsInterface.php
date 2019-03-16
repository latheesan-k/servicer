<?php

namespace MVF\Servicer;

interface SettingsInterface
{
    public function getName(): string;

    public function isCircuitBreakerClosed(): bool;

    public function isOldMessage(int $timestamp, \stdClass $headers, callable $consumeMessage): void;
}
