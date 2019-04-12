<?php

namespace MVF\Servicer;

interface EventPayloadInterface
{
    public function toPayload(): array;
}
