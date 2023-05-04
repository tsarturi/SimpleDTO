<?php

namespace Tsarturi\SimpleDTO\Castings;

use Throwable;
use Carbon\CarbonImmutable;
use Tsarturi\SimpleDTO\Exceptions\CastException;


class CarbonImmutableCast implements Castable
{
    public function __construct(
        private ?string $timezone = null,
        private ?string $format = null
    ) {
    }

    /**
     * @throws CastException
     */
    public function cast(string $property, mixed $value): CarbonImmutable
    {
        try {
            return is_null($this->format)
                ? CarbonImmutable::parse($value, $this->timezone)
                : CarbonImmutable::createFromFormat($this->format, $value, $this->timezone);
        } catch (Throwable) {
            throw new CastException($property);
        }
    }
}
