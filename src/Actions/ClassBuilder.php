<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;
use function Functional\map;

class ClassBuilder
{
    private static $instances = [];

    /**
     * Get the specified action from the list of defined actions.
     *
     * @param string $event Name of the event
     *
     * @return ActionInterface
     */
    public function buildActionFor(string $event): ActionInterface
    {
        return $this->buildClass(Constant::getAction($event));
    }

    public static function setInstance(string $class, $instance)
    {
        self::$instances[$class] = $instance;
    }

    public static function clean()
    {
        self::$instances = [];
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

        return $this->checkIfInstanceIsSet($class, $injections);
    }

    private function checkIfInstanceIsSet($class, $injections)
    {
        if (isset(self::$instances[$class])) {
            return self::$instances[$class];
        }

        return new $class(...$injections);
    }

    private function buildClassWithInjections(array $classWithInjections): array
    {
        [$class, $injectedClasses] = count($classWithInjections) > 1 ? $classWithInjections : [$classWithInjections[0], []];

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
