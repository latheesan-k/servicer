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

            return $this->buildClass($class);
        }

        return new UndefinedAction();
    }

    /**
     * @param string|array $class
     *
     * @return mixed
     */
    private function buildClass($class)
    {
        $injections = [];
        if (is_array($class) === true) {
            [$class, $injections] = $this->buildClassWithInjections($class);
        }

        return new $class(...$injections);
    }

    private function buildClassWithInjections(array $classWithInjections): array
    {
        [$class, $injectedClasses] = $classWithInjections;
        if (isset($injectedClasses) === false) {
            $injectedClasses = [];
        }

        $injections = [];
        foreach ($injectedClasses as $injectedClass) {
            $injections[] = $this->buildClass($injectedClass);
        }

        return [$class, $injections];
    }
}
