<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;
use MVF\Servicer\StandardConditions;

class ActionMockA extends StandardConditions implements ActionInterface
{
    /**
     * Executes the action.
     *
     * @param \stdClass $headers Headers of the event
     * @param \stdClass $body    Body of the event
     *
     * @throws \Exception used in tests
     */
    public function handle(\stdClass $headers, \stdClass $body): void
    {
        throw new \Exception('action_mock_a');
    }
}
