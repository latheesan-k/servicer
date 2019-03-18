<?php

namespace MVF\Servicer;

interface SettingsInterface
{
    /**
     * Gets the name of the queue.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Determines if message should be consumed.
     *
     * @param callable  $receive Callback function that triggers the consumption of the message
     */
    public function beforeReceive(callable $receive): void;
}
