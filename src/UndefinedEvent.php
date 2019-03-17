<?php

namespace MVF\Servicer;

class UndefinedEvent extends StandardConditions implements ActionInterface
{
    /**
     * Executes the action.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body    Body of the event
     *
     * @throws \Exception Indicates critical package failure
     */
    public function handle(\stdClass $headers, \stdClass $body): void
    {
        $message = 'UndefinedAction handle should never be called, there is a problem with the package please notify maintainer.';

        throw new \Exception($message);
    }
}
