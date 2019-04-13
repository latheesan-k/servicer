<?php

namespace MVF\Servicer\Mocks;

use MVF\Servicer\EventPayload;
use MVF\Servicer\EventPayloadInterface;

class EventBMock implements EventPayloadInterface
{
    use EventPayload;

    public $town = 'SWAGTOWN';
}
