<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;

class BuilderFacade
{
    /**
     * Creates an action class for the specified event.
     *
     * @param string|array $event Action class name
     *
     * @return ActionInterface
     */
    public static function buildActionFor(string $event): ActionInterface
    {
        $builder = new ClassBuilder();

        return $builder->buildActionFor($event);
    }

    /**
     * Gets the name of he events class.
     *
     * @param string $queue The name of the queue
     *
     * @return string
     */
    public static function getEventsClass(string $queue): string
    {
        return Constant::getBuilderFor($queue);
    }
}
