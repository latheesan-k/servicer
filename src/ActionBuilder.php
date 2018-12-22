<?php

namespace MVF\Servicer;

class ActionBuilder implements ActionsInterface
{
    /**
     * Get the specified action from the list of defined actions.
     *
     * @param null|string $name Name of the action
     *
     * @return ActionInterface
     */
    final public function getAction(?string $name): ActionInterface
    {
        if (defined('static::' . $name) === true) {
            $class = constant('static::' . $name);

            return new $class();
        }

        return new UndefinedAction();
    }
}
