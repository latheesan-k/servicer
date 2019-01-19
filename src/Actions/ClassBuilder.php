<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;
use MVF\Servicer\Exceptions\InvalidInjectionException;
use function Functional\map;

class ClassBuilder
{
    /**
     * Get the specified action from the list of defined actions.
     *
     * @param null|string $event Name of the event
     *
     * @return ActionInterface
     * @throws InvalidInjectionException
     */
    public function buildActionFor(?string $event): ActionInterface
    {
        return $this->buildClass(Constant::getAction($event));
    }

    /**
     * @param string|array $class
     *
     * @return mixed
     * @throws InvalidInjectionException
     */
    private function buildClass($class)
    {
        $injections = [];
        if (is_array($class) === true && count($class) > 1) {
            [$class, $injections] = $this->buildClassWithInjections($class);
        } elseif (is_array($class) === true) {
            throw new InvalidInjectionException($class);
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
