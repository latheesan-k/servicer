<?php
namespace Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use MVF\Servicer\Services\LogCapsule;
use Symfony\Component\Console\Application;

class Connection extends \Codeception\Module
{
    public function _initialize()
    {
        LogCapsule::setup(new Application());
    }
}
