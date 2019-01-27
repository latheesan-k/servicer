<?php

namespace MVF\Servicer;

interface BuilderInterface
{
    public function getHandlerClass(string $queue): string;
}
