<?php

namespace MVF\Servicer;

use MVF\Servicer\Actions\ActionBuilderFacade;
use MVF\Servicer\Actions\ActionMockA;
use Symfony\Component\Console\Output\ConsoleOutput;

class EventHandler extends ConsoleOutput implements EventInterface
{
    const MOCK = ActionMockA::class;

    public function triggerAction(\stdClass $headers, \stdClass $body): void
    {
        $source = static::class . '::' . $headers->event;
        $action = ActionBuilderFacade::buildActionFor($source);
        $action->handle($headers, $body);

        if (!($action instanceof UndefinedAction)) {
            $this->writeln('Event processed: ' . \GuzzleHttp\json_encode($headers) . ' ' . \GuzzleHttp\json_encode($body));
        }
    }
}
