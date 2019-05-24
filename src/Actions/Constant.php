<?php

namespace MVF\Servicer\Actions;

class Constant
{
    /**
     * Gets action class name.
     *
     * @param string $event The full constant name of the event
     *
     * @return string|array
     */
    public static function getAction(string $event)
    {
        if (defined($event) === true) {
            return constant($event);
        }

        return 'UndefinedEvent';
    }

    /**
     * Gets queue class name.
     *
     * @param string $queue The full constant name of the queue
     *
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
