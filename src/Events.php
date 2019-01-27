<?php

namespace MVF\Servicer;

use MVF\Servicer\Actions\ActionMockA;
use MVF\Servicer\Actions\BuilderFacade;
use Symfony\Component\Console\Output\ConsoleOutput;
use function GuzzleHttp\json_encode;

class Events extends ConsoleOutput implements EventsInterface
{
    const __MOCK__ = ActionMockA::class;
    const __UNDEFINED__ = 'Event is not defined: ';
    const __PROCESSED__ = 'Event processed: ';

    public function triggerAction(\stdClass $headers, \stdClass $body): void
    {
        $source = static::class . '::' . $headers->event;
        $action = BuilderFacade::buildActionFor($source);

        if ($action instanceof UndefinedEvent) {
            $this->eventHandled(self::__UNDEFINED__, $headers, $body);
        } else {
            $action->handle($headers, $body);
            $this->eventHandled(self::__PROCESSED__, $headers, $body);
        }
    }

    private function eventHandled(string $kind, $headers, $body): void
    {
        $this->writeln($kind . json_encode($headers) . ' ' . json_encode($body));
    }
}
