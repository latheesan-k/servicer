<?php

namespace MVF\Servicer;

use function Functional\map;

trait EventPayload
{
    public function toPayload(): array
    {
        $attributes = get_object_vars($this);
        $keys = map(array_keys($attributes), $this->transformToSnakeCase());
        $values = map(array_values($attributes), $this->transformToPayload());

        return array_combine($keys, $values);
    }

    private function transformToSnakeCase(): callable
    {
        return function ($key) {
            return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $key));
        };
    }

    private function transformToPayload(): callable
    {
        return function ($value) {
            if ($value instanceof EventPayloadInterface) {
                return $value->toPayload();
            }

            return $value;
        };
    }
}
