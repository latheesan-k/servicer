<?php

namespace MVF\Servicer;

interface QueueInterface
{
    /**
     * Listen to the queue.
     */
    public function listen(): void;
}
