<?php

namespace MVF\Servicer\Actions;

use MVF\Servicer\UndefinedEvent;

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

        return UndefinedEvent::class;
    }

    /**
     * @param  string $queue
     * @return string
     */
    public static function getBuilderFor(string $queue): string
    {
        if (defined($queue) === true) {
            return constant($queue);
        }

        return '';
    }
}
