<?php

use MVF\Servicer\ActionInterface;
use MVF\Servicer\Queues\SqsQueue;
use MVF\Servicer\Consumer;
use Symfony\Component\Console\Output\ConsoleOutput;

require __DIR__.'/vendor/autoload.php';

class Actions extends \MVF\Servicer\ActionBuilder
{
    const A = [
        A::class, [
            [ConsoleOutput::class, []]
        ]
    ];
}

class A implements ActionInterface
{
    /**
     * @var ConsoleOutput
     */
    private $output;

    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    /**
     * Executes the action.
     *
     * @param stdClass $headers Headers of the action
     * @param stdClass $body Body of the action
     */
    public function handle(stdClass $headers, stdClass $body): void
    {
        dump($headers, $body);
    }
}

$queue = new SqsQueue('mercury-admiral-campaigns');
(new Consumer($queue))->handle(new Actions());
