<?php

namespace MVF\Servicer;

use Symfony\Component\Console\Output\ConsoleOutput;

class UndefinedAction extends ConsoleOutput implements ActionInterface
{
    /**
     * Executes the action.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body    Body of the event
     */
    public function handle(\stdClass $headers, \stdClass $body): void
    {
        $this->writeln('Event is not defined: ' . \GuzzleHttp\json_encode($headers) . ' ' . \GuzzleHttp\json_encode($body));
    }
}
