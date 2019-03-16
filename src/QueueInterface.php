<?php

namespace MVF\Servicer;

use Symfony\Component\Console\Output\StreamOutput;

interface QueueInterface
{
    /**
     * Listen to the queue.
     */
    public function listen(): void;

    /**
     * Sets the console output class.
     *
     * @param StreamOutput $output Console output class
     */
    public function setConsoleOutput(StreamOutput $output): void;
}
