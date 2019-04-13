<?php

namespace MVF\Servicer\Messages;

interface EventMessageInterface
{
    /**
     * Returns the type of the provider.
     *
     * @return string
     */
    public function getProvider(): string;

    /**
     * Constructs array of object's attributes and values and transforms attributes to snake case.
     *
     * @return array
     */
    public function toPayload(): array;
}
