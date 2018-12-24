<?php

namespace MVF\Servicer;

use Symfony\Component\Console\Command\Command;

class BaseCommand extends Command
{
    /**
     * Defines available actions.
     *
     * @var ActionsInterface
     */
    private $actions;

    /**
     * Set the list of actions.
     *
     * @param ActionsInterface $actions Defines available actions
     */
    public function setActions(ActionsInterface $actions)
    {
        $this->actions = $actions;
    }

    /**
     * Get the specified action from the list of defined actions.
     *
     * @return ActionsInterface
     */
    protected function getActions(): ActionsInterface
    {
        return $this->actions;
    }
}
