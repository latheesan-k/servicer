<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\ActionInterface;

class ActionBuilderFacade
{
    public static function buildActionFor(?string $event): ActionInterface
    {
        return (new ClassBuilder())->buildActionFor($event);
    }
}
