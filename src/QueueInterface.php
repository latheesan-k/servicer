<?php

namespace MVF\Servicer;

interface QueueInterface
{
    /**
     * Listen to the queue.
     */
    public function listen(): void;

    /**
     * Sets the debug function.
     *
     * @param callable $debug Function that logs a debug message
     */
    public function setDebugFunction(callable $debug): void;
}
