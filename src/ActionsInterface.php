<?php

namespace MVF\Servicer;

interface ActionsInterface
{
    /**
     * Get the specified action from the list of defined actions.
     *
     * @param null|string $name Name of the action
     *
     * @return ActionInterface
     */
    public function getAction(?string $name): ActionInterface;
}
