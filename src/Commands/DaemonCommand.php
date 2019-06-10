<?php

namespace MVF\Servicer\Commands;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DaemonCommand extends Command
{
    const QUEUE = 'queue';

    private $queues;

    /**
     * DaemonCommand constructor.
     *
     * @param array $queues The list of queues to be polled
     */
    public function __construct(array $queues)
    {
        $this->queues = $queues;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('daemon');
        $this->setDescription('Run blocking daemon that listens for events');
        $this->setHelp('Not implemented');

        $this->addArgument(
            self::QUEUE,
            InputArgument::REQUIRED,
            'The queue where the event handler is defined'
        );
    }

    /**
     * Defines the behaviour of the command.
     *
     * @param InputInterface  $input  Defines console inputs
     * @param OutputInterface $output Defines console outputs
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $queue = $input->getArgument(self::QUEUE);

        try {
            $this->queues[$queue]->listen();
        } catch (\Exception $exception) {
            $output->writeln($exception->getMessage());
        }
    }
}
