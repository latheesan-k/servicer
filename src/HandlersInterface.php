<?php

namespace MVF\Servicer;

interface HandlersInterface
{
    public function getHandler(string $queue): string;
}
