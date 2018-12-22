<?php

namespace MVF\Servicer;

class UndefinedAction implements ActionInterface
{
    /**
     * Executes the action.
     *
     * @param array  $headers Headers of the action
     * @param string $body    Body of the action
     */
    public function handle(array $headers, string $body): void
    {
        echo 'Received undefined action "' . $headers['action'] . '"' . PHP_EOL;
    }
}
