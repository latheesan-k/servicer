<?php

namespace MVF\Servicer;

use MVF\Servicer\Commands\DaemonCommand;
use MVF\Servicer\Commands\ExecCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;

class Consumer extends ConsoleOutput
{
    /**
     * Defines console application.
     *
     * @var Application
     */
    private $application;

    /**
     * Service constructor.
     *
     * @param QueueInterface[] $queues defines the queue driver used by the daemon
     */
    public function __construct(QueueInterface ...$queues)
    {
        $this->application = new Application();

        $daemon = new ExecCommand(...$queues);
        $this->application->add($daemon);

        $daemon = new DaemonCommand(...$queues);
        $this->application->add($daemon);

        parent::__construct();
    }

    /**
     * Parses the params and runs the specified command.
     */
    public function handle()
    {
        try {
            $this->application->run();
        } catch (\Exception $e) {
            $this->writeln($e->getMessage());
            exit(1);
        }
    }
}
