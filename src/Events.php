<?php

namespace MVF\Servicer;

use MVF\Servicer\Actions\ActionMockA;
use MVF\Servicer\Actions\BuilderFacade;
use Symfony\Component\Console\Output\ConsoleOutput;
use function GuzzleHttp\json_encode;

class Events extends ConsoleOutput
{
    const __MOCK__ = ActionMockA::class;
    const __UNDEFINED__ = 'Event is not defined: ';
    const __PROCESSED__ = 'Event processed: ';

    /**
     * Run the correct action based on the event in the header.
     *
     * @param \stdClass $headers Attributes of the message headers
     * @param \stdClass $body    Attributes of the message body
     */
    public function triggerAction(\stdClass $headers, \stdClass $body): void
    {
        $source = static::class . '::' . $headers->event;
        $action = BuilderFacade::buildActionFor($source);

        if ($action instanceof UndefinedEvent) {
            $this->eventHandled(self::__UNDEFINED__, $headers, $body);
        } else {
            $consumeMessage = $this->consumeMessage($action, $headers, $body);
            $action->beforeReceive($headers, $consumeMessage);
        }
    }

    /**
     * Higher order function that consumes the message.
     *
     * @param ActionInterface $action  Action to be executed
     * @param \stdClass       $headers Attributes of the message headers
     * @param \stdClass       $body    Attributes of the message body
     *
     * @return callable
     */
    private function consumeMessage(ActionInterface $action, \stdClass $headers, \stdClass $body): callable
    {
        return function () use ($action, $headers, $body) {
            $action->handle($headers, $body);
            $this->eventHandled(self::__PROCESSED__, $headers, $body);
        };
    }

    /**
     * Logs whether the event was handled.
     *
     * @param string    $kind    The kind of log
     * @param \stdClass $headers Attributes of the message headers
     * @param \stdClass $body    Attributes of the message body
     */
    private function eventHandled(string $kind, \stdClass $headers, \stdClass $body): void
    {
        $this->writeln($kind . json_encode($headers) . ' ' . json_encode($body));
    }
}
