<?php

namespace MVF\Servicer\Consumer\Exceptions;

class NoMessagesException extends \Exception
{
    public function __construct()
    {
        parent::__construct('No Message Received');
    }
}
