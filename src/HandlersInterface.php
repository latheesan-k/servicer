<?php

namespace MVF\Servicer;

interface HandlersInterface
{
    public function getHandlerClass(string $queue): string;
}
