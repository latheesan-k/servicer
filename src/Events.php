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
     * @param array $headers Attributes of the message headers
     * @param array $body    Attributes of the message body
     */
    public function triggerAction(array $headers, array $body): void
    {
        $source = static::class . '::' . $headers['event'];
        $action = BuilderFacade::buildActionFor($source);

        if ($action instanceof UndefinedEvent) {
            $this->log('WARNING', get_class($action), 'IGNORED', $headers, $body);
        } else {
            $consumeMessage = $this->consumeMessage($action, $headers, $body);
            $action->beforeAction($headers, $body, $consumeMessage);
        }
    }

    /**
     * Higher order function that consumes the message.
     *
     * @param ActionInterface $action  Action to be executed
     * @param array           $headers Attributes of the message headers
     * @param array           $body    Attributes of the message body
     *
     * @return callable
     */
    private function consumeMessage(ActionInterface $action, array $headers, array $body): callable
    {
        return function () use ($action, $headers, $body) {
            $this->log('INFO', get_class($action), 'STARTED', $headers, $body);
            $action->handle($headers, $body);
            $this->log('INFO', get_class($action), 'COMPLETED', $headers, $body);
        };
    }

    /**
     * Logs whether the event was handled.
     *
     * @param string $severity The severity of the message
     * @param string $action   The action being logged
     * @param string $state    The state of the event
     * @param array  $headers  Attributes of the message headers
     * @param array  $body     Attributes of the message body
     */
    private function log(string $severity, string $action, string $state, array $headers, array $body): void
    {
        $payload = [
            'severity' => $severity,
            'action' => $action,
            'state' => $state,
            'header' => $headers,
            'body' => $body,
        ];

        $this->writeln(json_encode($payload));
    }
}
