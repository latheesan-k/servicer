<?php

namespace MVF\Servicer;

interface ActionInterface
{
    /**
     * Executes the action.
     *
     * @param array  $headers Headers of the action
     * @param string $body    Body of the action
     */
    public function handle(array $headers, string $body): void;
}
