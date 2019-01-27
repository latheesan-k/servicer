<?php
/**
 * Created by PhpStorm.
 * User: drupsys
 * Date: 26/01/19
 * Time: 23:27.
 */

namespace MVF\Servicer;

use MVF\Servicer\Actions\BuilderFacade;

class Builder implements BuilderInterface
{
    const __MOCK__ = 'SomeObject';

    public function getHandlerClass(string $queue): string
    {
        $source = static::class . '::' . $queue;

        return (BuilderFacade::getEventsClass($source) ?? '');
    }
}
