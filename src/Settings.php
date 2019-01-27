<?php

namespace MVF\Servicer;

abstract class Settings implements SettingsInterface
{
    public function isCircuitBreakerClosed(): bool
    {
        return false;
    }
}
