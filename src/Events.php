<?php

namespace MVF\Servicer;

use MVF\Servicer\Actions\ActionMock;
use MVF\Servicer\Actions\BuilderFacade;
use MVF\Servicer\Actions\Constant;
use Symfony\Component\Console\Output\ConsoleOutput;
use function Functional\each;

class Events extends ConsoleOutput
{
    const __MOCK__ = [ActionMock::class];
    const __UNDEFINED__ = 'Event is not defined: ';
    const __PROCESSED__ = 'Event processed: ';

    /**
     * Run actions based on the event in the header.
     *
     * @param array $headers Attributes of the message headers
     * @param array $body    Attributes of the message body
     */
    public function triggerActions(array $headers, array $body): void
    {
        $event = static::class . '::' . $headers['event'];
        $actions = Constant::getActions($event);

        if (empty($actions) === true) {
            MessageConsumer::log('WARNING', 'UNDEFINED_EVENT', 'IGNORED', $headers, $body);
        } else {
            each($actions, $this->triggerAction($headers, $body));
        }
    }

    /**
     * Run the correct action.
     *
     * @param array $headers Attributes of the message headers
     * @param array $body    Attributes of the message body
     *
     * @return callable
     */
    private function triggerAction(array $headers, array $body): callable
    {
        return function ($class) use ($headers, $body) {
            $action = BuilderFacade::buildActionFor($class);
            $consumeMessage = MessageConsumer::consume($action, $headers, $body);
            $action->beforeAction($headers, $body, $consumeMessage);
        };
    }
}
