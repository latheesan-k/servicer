<?php

namespace MVF\Servicer;

interface ActionInterface
{
    /**
     * Determines if message should be consumed.
     *
     * @param array $headers Headers of the event
     * @param array $body Payload of the event
     * @param callable $consume Callback function that should be called if message should be consumed
     */
    public function beforeAction(array $headers, array $body, callable $consume): void;

    /**
     * Executes the action.
     *
     * @param array $headers Headers of the event
     * @param array $body Body of the event
     */
    public function handle(array $headers, array $body): void;
}
