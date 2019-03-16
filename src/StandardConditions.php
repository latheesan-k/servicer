<?php

namespace MVF\Servicer;

abstract class StandardConditions implements ActionInterface
{
    /**
     * Determines if message should be consumed.
     *
     * @param \stdClass $headers     Headers of the event
     * @param callable  $thenReceive Callback function that should be called if message should be consumed
     */
    public function beforeReceive(\stdClass $headers, callable $thenReceive): void
    {
        $thenReceive();
    }
}
