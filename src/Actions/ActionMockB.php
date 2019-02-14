<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;
use MVF\Servicer\StandardConditions;
use Symfony\Component\Console\Output\ConsoleOutput;

class ActionMockB extends StandardConditions implements ActionInterface
{
    /**
     * @var ConsoleOutput
     */
    private $output;

    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    /**
     * Executes the action.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body    Body of the event
     */
    public function handle(\stdClass $headers, \stdClass $body): void
    {
        $this->output->writeln('test');
    }
}
