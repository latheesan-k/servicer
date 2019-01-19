<?php

namespace MVF\Servicer;

interface EventInterface
{
    public function triggerAction(\stdClass $headers, \stdClass $body): void;
}
