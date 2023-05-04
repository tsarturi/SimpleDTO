<?php

namespace Tsarturi\SimpleDTO\Castings;

use Throwable;
use Tsarturi\SimpleDTO\DTO;
use Illuminate\Validation\ValidationException;
use Tsarturi\SimpleDTO\Exceptions\CastException;
use Tsarturi\SimpleDTO\Exceptions\CastTargetException;

class DTOCast implements Castable
{
    public function __construct(private string $dtoClass)
    {
    }

    /**
     * @throws CastException|CastTargetException|ValidationException
     */
    public function cast(string $property, mixed $value): DTO
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value)) {
            throw new CastException($property);
        }

        try {
            $dto = new $this->dtoClass($value);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Throwable) {
            throw new CastException($property);
        }

        if (! ($dto instanceof DTO)) {
            throw new CastTargetException($property);
        }

        return $dto;
    }
}
