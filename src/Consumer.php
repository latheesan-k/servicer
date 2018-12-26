<?php

namespace MVF\Servicer;

use MVF\Servicer\Commands\DaemonCommand;
use MVF\Servicer\Commands\ExecCommand;
use MVF\Servicer\Queues\SqsQueue;
use Symfony\Component\Console\Application;

class Consumer
{
    /**
     * Defines console application.
     *
     * @var Application
     */
    private $application;
    /**
     * Defines the list of commands.
     *
     * @var BaseCommand[]
     */
    private $commands;

    /**
     * Service constructor.
     *
     * @param null|QueueInterface $queue Defines the queue driver used by the daemon.
     */
    public function __construct(QueueInterface $queue)
    {
        $this->application = new Application();

        $exec = new ExecCommand();
        $this->application->add($exec);
        $this->commands[] = $exec;

        $daemon = new DaemonCommand($queue);
        $this->application->add($daemon);
        $this->commands[] = $daemon;
    }

    /**
     * Parses the params and runs the specified command.
     *
     * @param ActionsInterface $actions Defines the list of actions
     */
    public function handle(ActionsInterface $actions)
    {
        foreach ($this->commands as $command) {
            $command->setActions($actions);
        }

        try {
            $this->application->run();
        } catch (\Exception $e) {
            exit(1);
        }
    }
}
