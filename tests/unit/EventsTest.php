<?php

namespace MVF\Servicer\Tests;

use AspectMock\Test;
use MVF\Servicer\ActionInterface;
use MVF\Servicer\Actions\BuilderFacade;
use MVF\Servicer\Events;
use MVF\Servicer\UndefinedEvent;

class EventsTest extends \Codeception\Test\Unit
{
    public function _after()
    {
        Test::clean();
    }

    public function testBuildActionReturnsInvalidActionIfEventIsNotFound()
    {
        $test = function (string $message) {
            $this->assertContains('Event is not defined', $message);
        };
        $action = $this->make(UndefinedEvent::class, ['writeln' => $test]);
        $buildActionFor = function () use ($action) {
            return $action;
        };
        Test::double(BuilderFacade::class, ['buildActionFor' => $buildActionFor]);

        $handler = $this->make(Events::class);
        $handler->triggerAction((object)['event' => 'TEST'], (object)[]);
    }

    public function testBuildActionReturnsValidAction()
    {
        $action = $this->makeEmpty(ActionInterface::class);
        $buildActionFor = function () use ($action) {
            return $action;
        };
        Test::double(BuilderFacade::class, ['buildActionFor' => $buildActionFor]);

        $test = function (string $message) {
            $this->assertContains('Event processed', $message);
        };
        $handler = $this->make(Events::class, ['writeln' => $test]);

        $handler->triggerAction((object)['event' => 'TEST'], (object)[]);
    }
}
