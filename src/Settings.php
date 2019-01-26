<?php

namespace MVF\Servicer;

abstract class Settings implements SettingsInterface
{
    abstract public function getName(): string;

    public function isCircuitBreakerClosed(): bool
    {
        return false;
    }
}
