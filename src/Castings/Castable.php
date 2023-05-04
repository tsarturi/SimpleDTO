<?php

namespace Tsarturi\SimpleDTO\Castings;

interface Castable
{
    /**
     * Casts the given value.
     */
    public function cast(string $property, mixed $value): mixed;
}
