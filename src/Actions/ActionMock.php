<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;
use MVF\Servicer\AlwaysConsumeAction;

class ActionMock implements ActionInterface
{
    use AlwaysConsumeAction;

    /**
     * Executes the action.
     *
     * @param array $headers Headers of the event
     * @param array $body    Body of the event
     *
     * @throws \Exception used in tests
     */
    public function handle(array $headers, array $body): void
    {
        throw new \Exception('action_mock_a');
    }
}
