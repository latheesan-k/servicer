<?php

namespace MVF\Servicer;

abstract class StandardConditions implements ActionInterface
{
    /**
     * Determines if message should be consumed.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body    Payload of the event
     * @param callable  $consume Callback function that should be called if message should be consumed
     */
    public function beforeAction(\stdClass $headers, \stdClass $body, callable $consume): void
    {
        $consume();
    }
}
