<?php

namespace Tsarturi\SimpleDTO\Castings;

use Tsarturi\SimpleDTO\Exceptions\CastException;

class IntegerCast implements Castable
{
    /**
     * @throws CastException
     */
    public function cast(string $property, mixed $value): int
    {
        if (! is_numeric($value)) {
            throw new CastException($property);
        }

        return (int) $value;
    }
}
