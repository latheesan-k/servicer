<?php

namespace MVF\Servicer;

abstract class Config implements ConfigInterface
{
    abstract public function getName(): string;

    public function skip(): bool
    {
        return false;
    }
}
