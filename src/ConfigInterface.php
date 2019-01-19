<?php

namespace MVF\Servicer\Consumer;

interface ConfigInterface
{
    public function getName(): string;

    public function skip(): bool;
}
