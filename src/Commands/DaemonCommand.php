<?php

namespace MVF\Servicer\Commands;

use MVF\Servicer\BaseCommand;
use MVF\Servicer\QueueInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DaemonCommand extends BaseCommand
{
    const QUEUE = 'queue';
    /**
     * @var QueueInterface
     */
    private $queue;

    /**
     * DaemonCommand constructor.
     *
     * @param QueueInterface $queue
     */
    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
        parent::__construct();
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('daemon');
        $this->setDescription('Run blocking daemon that listens for actions');
        $this->setHelp('Not implemented');

        $this->addArgument(
            self::QUEUE,
            InputArgument::REQUIRED,
            'The queue to be listened'
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
        $this->queue->listen($this->getActions(), $input, $output);
    }
}
