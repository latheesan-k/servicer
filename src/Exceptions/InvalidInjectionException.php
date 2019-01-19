<?php

namespace MVF\Servicer\Exceptions;

class InvalidInjectionException extends \Exception
{
    public function __construct(array $injections)
    {
        parent::__construct('Cannot build class, invalid injections provided ' . \GuzzleHttp\json_encode($injections));
    }
}
