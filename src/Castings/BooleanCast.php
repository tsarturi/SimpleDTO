<?php

namespace Tsarturi\SimpleDTO\Castings;

class BooleanCast implements Castable
{
    public function cast(string $property, mixed $value): bool
    {
        if (is_numeric($value)) {
            return $value > 0;
        }

        if (is_string($value)) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return boolval($value);
    }
}
