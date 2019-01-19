<?php namespace MVF\Servicer\Tests;

use AspectMock\Test;
use MVF\Servicer\ActionInterface;
use MVF\Servicer\Actions\ActionBuilderFacade;
use MVF\Servicer\EventHandler;
use MVF\Servicer\UndefinedAction;

class EventHandlerTest extends \Codeception\Test\Unit
{
    public function testBuildActionReturnsInvalidActionIfEventIsNotFound()
    {
        $test = function (string $message) {
            $this->assertContains('Event is not defined', $message);
        };
        $action = $this->make(UndefinedAction::class, ['writeln' => $test]);
        $buildActionFor = function () use ($action) {
            return $action;
        };
        Test::double(ActionBuilderFacade::class, ['buildActionFor' => $buildActionFor]);

        $handler = $this->make(EventHandler::class);
        $handler->triggerAction((object)['event' => 'TEST'], (object)[]);
    }

    public function testBuildActionReturnsValidAction()
    {
        $action = $this->makeEmpty(ActionInterface::class);
        $buildActionFor = function () use ($action) {
            return $action;
        };
        Test::double(ActionBuilderFacade::class, ['buildActionFor' => $buildActionFor]);

        $test = function (string $message) {
            $this->assertContains('Event processed', $message);
        };
        $handler = $this->make(EventHandler::class, ['writeln' => $test]);

        $handler->triggerAction((object)['event' => 'TEST'], (object)[]);
    }
}
