<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;
use MVF\Servicer\StandardConditions;

class ActionMockA extends StandardConditions implements ActionInterface
{
    public function handle(\stdClass $headers, \stdClass $body): void
    {
        throw new \Exception('action_mock_a');
    }
}
