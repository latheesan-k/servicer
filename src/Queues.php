<?php

namespace MVF\Servicer;

use MVF\Servicer\Actions\BuilderFacade;

class Queues
{
    const __MOCK__ = 'SomeObject';

    /**
     * Gets the class name of the specified queue.
     *
     * @param string $queue The name of the queue
     *
     * @return string
     */
    public function getClass(string $queue): string
    {
        $source = static::class . '::' . $queue;

        return (BuilderFacade::getEventsClass($source) ?? '');
    }
}
