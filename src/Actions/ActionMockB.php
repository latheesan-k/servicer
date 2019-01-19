<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class ActionMockB implements ActionInterface
{
    public function __construct(ConsoleOutput $output)
    {
    }

    /**
     * Executes the action.
     *
     * @param \stdClass $headers Headers of the action
     * @param \stdClass $body    Body of the action
     */
    public function handle(\stdClass $headers, \stdClass $body): void
    {
        // TODO: Implement handle() method.
    }
}
