<?php

namespace MVF\Servicer;

interface SettingsInterface
{
    public function getName(): string;

    public function isCircuitBreakerClosed(): bool;
}
