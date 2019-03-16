<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 26/01/19
 * Time: 23:27.
 */

namespace MVF\Servicer;

use MVF\Servicer\Actions\BuilderFacade;

class EventsBuilder
{
    const __MOCK__ = 'SomeObject';

    public function getClass(string $queue): string
    {
        $source = static::class . '::' . $queue;

        return (BuilderFacade::getEventsClass($source) ?? '');
    }
}
