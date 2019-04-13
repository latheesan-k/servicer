<?php

namespace MVF\Servicer\Mocks;

use MVF\Servicer\EventPayload;
use MVF\Servicer\EventPayloadInterface;

class EventAMock implements EventPayloadInterface
{
    use EventPayload;

    public $firstName;
    public $invalidObject;
    public $address;
    private $privateAttribute = 'test';
}
