<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\UndefinedAction;

class Constant
{
    /**
     * @param  string $event
     * @return string|array
     */
    public static function getAction(string $event)
    {
        if (defined($event) === true) {
            return constant($event);
        }

        return UndefinedAction::class;
    }
}
