<?php

namespace MVF\Servicer;

abstract class StandardConditions implements ActionInterface
{
    public function skipMessage(\stdClass $headers, callable $consumeMessage): void
    {
        $consumeMessage();
    }
}
