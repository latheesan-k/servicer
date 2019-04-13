<?php

namespace MVF\Servicer;

abstract class StandardConditions implements ActionInterface
{
    /**
     * Determines if message should be consumed.
     *
     * @param array    $headers Headers of the event
     * @param array    $body    Payload of the event
     * @param callable $consume Callback function that should be called if message should be consumed
     */
    public function beforeAction(array $headers, array $body, callable $consume): void
    {
        $consume();
    }
}
