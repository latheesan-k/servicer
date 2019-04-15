<?php

namespace MVF\Servicer\Mocks;

use MVF\Servicer\EventHeaders;
use MVF\Servicer\EventPayloadInterface;

class HeaderAMock implements EventPayloadInterface
{
    use EventHeaders;

    /**
     * HeaderAMock constructor.
     */
    public function __construct()
    {
        $this->initialise('TEST_EVENT', '1.0.0');
    }

    /**
     * Runs loadRemaining in the EventHeaders trait.
     *
     * @param array $headers To be loaded
     */
    public function from(array $headers)
    {
        $this->loadRemaining($headers);
    }
}
