<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;

class BuilderFacade
{
    public static function buildActionFor(string $event): ActionInterface
    {
        return (new ClassBuilder())->buildActionFor($event);
    }

    public static function getEventsClass(string $queue): string
    {
        return Constant::getBuilderFor($queue);
    }
}
