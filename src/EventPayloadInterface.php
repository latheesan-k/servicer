<?php

namespace MVF\Servicer;

interface EventPayloadInterface
{
    /**
     * Constructs array of object's attributes and values and transforms attributes to snake case.
     *
     * @return array
     */
    public function toPayload(): array;
}
