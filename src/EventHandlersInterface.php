<?php

namespace MVF\Servicer;

interface EventHandlersInterface
{
    public function getEventHandler(string $queue): string;
}
