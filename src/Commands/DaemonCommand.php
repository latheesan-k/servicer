<?php

namespace MVF\Servicer\Commands;

use MVF\Servicer\QueueInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Functional\each;
use function Functional\invoker;

class DaemonCommand extends Command
{
    /**
     * @var QueueInterface[]
     */
    private $queues;

    /**
     * DaemonCommand constructor.
     *
     * @param QueueInterface ...$queues
     */
    public function __construct(QueueInterface ...$queues)
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
        while (true) {
            each($this->queues, invoker('listen'));
            usleep(100);
        }
    }
}
