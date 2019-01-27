<?php

namespace MVF\Servicer;

use Symfony\Component\Console\Output\ConsoleOutput;

class UndefinedEvent extends ConsoleOutput implements ActionInterface
{
    /**
     * Executes the action.
     *
     * @param  \stdClass $headers Headers of the event
     * @param  \stdClass $body    Body of the event
     * @throws \Exception
     */
    public function handle(\stdClass $headers, \stdClass $body): void
    {
        $message = 'UndefinedAction interface should never be called'
            . ', there is a problem with the package please notify maintainer.';

        throw new \Exception($message);
    }
}
