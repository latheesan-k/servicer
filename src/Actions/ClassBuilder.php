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

    /**
     * Mocks the specified class with the provided instance.
     *
     * @param string $class    The class to be mocked
     * @param mixed  $instance Instance to be used
     */
    public static function setInstance(string $class, $instance)
    {
        self::$instances[$class] = $instance;
    }

    /**
     * Removes mocks.
     */
    public static function clean()
    {
        self::$instances = [];
    }

    /**
     * Constructs the specified class.
     *
     * @param string|array $class The class to be constructed
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

    /**
     * Checks if the specified class is mocked.
     *
     * @param string $class      Class name to be checked
     * @param array  $injections The list of injections for this class
     *
     * @return mixed
     */
    private function checkIfInstanceIsSet(string $class, array $injections)
    {
        if (isset(self::$instances[$class]) === true) {
            return self::$instances[$class];
        }

        return new $class(...$injections);
    }

    /**
     * Builds class with injections.
     *
     * @param array $classWithInjections Contains class name and its injections
     *
     * @return array
     */
    private function buildClassWithInjections(array $classWithInjections): array
    {
        [$class, $injectedClasses] = [$classWithInjections['class'], []];
        if (count($classWithInjections) > 1) {
            $class = $classWithInjections['class'];
            $injectedClasses = $classWithInjections['with'];
        }

        $injections = map($injectedClasses, $this->buildInjections());

        return [$class, $injections];
    }

    /**
     * Constructs injections.
     *
     * @return callable
     */
    private function buildInjections(): callable
    {
        return function ($injectedClass) {
            return $this->buildClass($injectedClass);
        };
    }
}
