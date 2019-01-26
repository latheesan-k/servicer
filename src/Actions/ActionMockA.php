<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;
use Symfony\Component\Console\Output\ConsoleOutput;

class ActionMockA extends ConsoleOutput implements ActionInterface
{
    /**
     * Executes the action.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body    Body of the event
     */
    public function handle(\stdClass $headers, \stdClass $body): void
    {
        $this->writeln("test");
    }
}
