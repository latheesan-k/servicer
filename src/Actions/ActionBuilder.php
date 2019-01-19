<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;
use MVF\Servicer\UndefinedAction;
use function Functional\map;

class ActionBuilder
{
    /**
     * Get the specified action from the list of defined actions.
     *
     * @param null|string $event Name of the event
     *
     * @return ActionInterface
     */
    public function buildActionFor(?string $event): ActionInterface
    {
        if (defined($event) === true) {
            $class = constant($event);

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

        $injections = map($injectedClasses, $this->buildInjections());

        return [$class, $injections];
    }

    private function buildInjections(): callable
    {
        return function ($injectedClass) {
            return $this->buildClass($injectedClass);
        };
    }
}
