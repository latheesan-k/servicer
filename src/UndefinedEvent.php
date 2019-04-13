<?php

namespace MVF\Servicer;

class UndefinedEvent extends StandardConditions implements ActionInterface
{
    /**
     * Executes the action.
     *
     * @param array $headers Headers of the event
     * @param array $body    Body of the event
     *
     * @throws \Exception Indicates critical package failure
     */
    public function handle(array $headers, array $body): void
    {
        $message = 'UndefinedAction handle should never be called';
        $message .= ', there is a problem with the package please notify maintainer.';

        throw new \Exception($message);
    }
}
