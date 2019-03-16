<?php

namespace MVF\Servicer\Commands;

use MVF\Servicer\Actions\BuilderFacade;
use MVF\Servicer\EventsBuilder;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ExecCommand extends Command
{
    const QUEUE = 'queue';
    const ACTION = 'action';
    const HEADERS = 'header';
    const BODY = 'body';

    /**
     * @var EventsBuilder
     */
    private $eventsBuilder;

    /**
     * ExecCommand constructor.
     *
     * @param EventsBuilder $eventsBuilder
     */
    public function __construct(EventsBuilder $eventsBuilder)
    {
        $this->eventsBuilder = $eventsBuilder;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('exec');
        $this->setDescription('Run specified action');
        $this->setHelp('Not implemented');
        $this->addArgument(self::QUEUE, InputArgument::REQUIRED, 'The queue where the event handler is defined');
        $this->addArgument(self::ACTION, InputArgument::REQUIRED, 'The action to be executed');

        $this->addOption(
            self::HEADERS,
            '-H',
            (InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY),
            'The list of headers',
            []
        );

        $this->addOption(
            self::BODY,
            '-b',
            InputOption::VALUE_OPTIONAL,
            'The payload of the event',
            '{}'
        );
    }

    /**
     * Defines the behaviour of the command.
     *
     * @param InputInterface  $input  Defines inputs
     * @param OutputInterface $output Defines outputs
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $headers = $this->getHeaders($input);
        $body = \GuzzleHttp\json_decode($input->getOption(self::BODY));

        $queue = $input->getArgument(self::QUEUE);
        $eventHandlerClass = $this->eventsBuilder->getClass($queue);
        $action = BuilderFacade::buildActionFor($eventHandlerClass . '::' . $headers->event);
        $action->handle($headers, $body);
    }

    private function getHeaders(InputInterface $input): \stdClass
    {
        $headers = (object)[];
        foreach ($input->getOption(self::HEADERS) as $header) {
            $pattern = '/^(\w*)=(.*)$/';
            if (preg_match($pattern, $header, $matches) !== false) {
                [$full, $key, $value] = $matches;
                $field = strtolower($key);
                $headers->$field = $value;
            }
        }

        $headers->event = $input->getArgument(self::ACTION);

        return $headers;
    }
}
