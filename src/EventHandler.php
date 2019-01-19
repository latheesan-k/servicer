<?php

namespace MVF\Servicer;

use Symfony\Component\Console\Output\ConsoleOutput;
use function Functional\map;

abstract class EventHandler extends ConsoleOutput implements EventInterface
{
    final public function triggerAction(\stdClass $headers, \stdClass $body): void
    {
        $action = $this->buildActionFor($headers->event);
        $action->handle($headers, $body);

        if (!($action instanceof UndefinedAction)) {
            $this->writeln('Event processed: ' . \GuzzleHttp\json_encode($headers) . ' ' . \GuzzleHttp\json_encode($body));
        }
    }

    /**
     * Get the specified action from the list of defined actions.
     *
     * @param null|string $event Name of the event
     *
     * @return ActionInterface
     */
    private function buildActionFor(?string $event): ActionInterface
    {
        if (defined('static::' . $event) === true) {
            $class = constant('static::' . $event);

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
