<?php

namespace MVF\Servicer;

use MVF\Servicer\Commands\DaemonCommand;
use MVF\Servicer\Commands\ExecCommand;
use Symfony\Component\Console\Application;

class Service
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
     * @param ActionsInterface $actions Defines the list of actions
     */
    public function __construct(ActionsInterface $actions)
    {
        $this->application = new Application();

        $exec = new ExecCommand($actions);
        $this->application->add($exec);

        $daemon = new DaemonCommand($actions);
        $this->application->add($daemon);
    }

    /**
     * Parses the params and runs the specified command.
     */
    public function handle()
    {
        try {
            $this->application->run();
        } catch (\Exception $e) {
            exit(1);
        }
    }
}
