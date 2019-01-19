<?php

namespace MVF\Servicer\Consumer;

interface EventInterface
{
    public function triggerAction(\stdClass $headers, \stdClass $body): void;
}
