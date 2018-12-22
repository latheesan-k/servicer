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
     * BaseCommand constructor.
     *
     * @param ActionsInterface $actions Defines available actions
     */
    public function __construct(ActionsInterface $actions)
    {
        parent::__construct();
        $this->actions = $actions;
    }

    /**
     * Get the specified action from the list of defined actions.
     *
     * @param null|string $name Name of the action
     *
     * @return ActionInterface
     */
    protected function getAction(?string $name): ActionInterface
    {
        return $this->actions->getAction($name);
    }
}
