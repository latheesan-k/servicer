<?php

namespace MVF\Servicer\Commands;

use MVF\Servicer\BaseCommand;
use MVF\Servicer\ErrorInterface;
use MVF\Servicer\QueueInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class DaemonCommand extends BaseCommand implements ErrorInterface
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

    public function handleException(\Exception $exception): void
    {
        $output = new ConsoleOutput();
        $output->writeln($exception->getMessage());
    }

    /**
     * Configures the current command.
     */
    protected function configure()
    {
        $this->setName('daemon');
        $this->setDescription('Run blocking daemon that listens for actions');
        $this->setHelp('Not implemented');
    }

    /**
     * Defines the behaviour of the command.
     *
     * @param InputInterface  $input  Defines inputs
     * @param OutputInterface $output Defines outputs
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->queue->listen($this->getActions(), $this);
    }
}
