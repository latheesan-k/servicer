<?php

namespace MVF\Servicer;

interface EventsInterface
{
    public function triggerAction(\stdClass $headers, \stdClass $body): void;
}
