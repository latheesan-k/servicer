<?php

namespace MVF\Servicer;

use MVF\Servicer\Actions\ActionMock;
use MVF\Servicer\Actions\BuilderFacade;
use MVF\Servicer\Actions\Constant;
use OpenTracing\GlobalTracer;
use ReflectionClass;
use Symfony\Component\Console\Output\ConsoleOutput;
use function GuzzleHttp\json_encode;

class Events extends ConsoleOutput
{
    const __MOCK__ = ActionMock::class;
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
        $event = static::class . '::' . $headers['event'];
        $class = Constant::getAction($event);

        if ($class === 'UNDEFINED_EVENT') {
            $this->log('WARNING', $class, 'IGNORED', $headers, $body);
        } else {
            $action = BuilderFacade::buildActionFor($class);
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
            $reflect = new ReflectionClass($action);

            if (isset($headers['carrier'])) {
                GlobalTracer::get()->extract('text_map', $headers['carrier']);
            }

            $span = GlobalTracer::get()->startActiveSpan($reflect->getShortName())->getSpan();
            $this->log('INFO', $reflect->getShortName(), 'STARTED', $headers, $body);
            $action->handle($headers, $body);
            $this->log('INFO', $reflect->getShortName(), 'COMPLETED', $headers, $body);
            $span->finish();
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
            'event' => $headers['event'] ?? 'UNDEFINED_EVENT',
            'action' => $action,
            'state' => $state,
            'message' => 'Payload: ' . json_encode(['headers' => $headers, 'body' => $body])
        ];

        $this->writeln(json_encode($payload));
    }
}
