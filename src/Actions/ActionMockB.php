<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;
use MVF\Servicer\AlwaysConsumeAction;
use Symfony\Component\Console\Output\ConsoleOutput;

class ActionMockB implements ActionInterface
{
    use AlwaysConsumeAction;

    private $output;

    /**
     * ActionMockB constructor.
     *
     * @param ConsoleOutput $output Console output
     */
    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    /**
     * Executes the action.
     *
     * @param array $headers Headers of the event
     * @param array $body    Body of the event
     */
    public function handle(array $headers, array $body): void
    {
        $this->output->writeln('test');
    }
}
