<?php

namespace MVF\Servicer\Commands;

use MVF\Servicer\QueueInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use function Functional\each;
use function Functional\invoker;

class DaemonCommand extends Command
{
    private $queues;
    private $delay = 100000;

    /**
     * DaemonCommand constructor.
     *
     * @param QueueInterface ...$queues The list of queues to be polled
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
        $this->setDescription('Run blocking daemon that listens for events');
        $this->setHelp('Not implemented');
        $this->addOption(
            'once',
            null,
            InputOption::VALUE_NONE,
            'Run only one iteration'
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
        each($this->queues, invoker('setDebugFunction', [$this->debug($output)]));

        do {
            $this->debug($output)('DEBUG: Main loop is running');
            each($this->queues, $this->invokeListen($output));
            usleep($this->delay);
        } while ($input->getOption('once') !== true);
    }

    /**
     * Listens to the queue and handles errors.
     *
     * @param OutputInterface $output Defines console outputs
     *
     * @return callable
     */
    private function invokeListen(OutputInterface $output): callable
    {
        return function (QueueInterface $queue) use ($output) {
            try {
                $queue->listen();
            } catch (\Exception $exception) {
                $output->writeln($exception->getMessage());
            }
        };
    }

    /**
     * Outputs debug message if debug flag is set.
     *
     * @param OutputInterface $output Defines console outputs
     *
     * @return callable
     */
    private function debug(OutputInterface $output): callable
    {
        return function (string $message) use ($output) {
            if ($output->isDebug() === true) {
                $output->writeln($message);
            }
        };
    }
}
