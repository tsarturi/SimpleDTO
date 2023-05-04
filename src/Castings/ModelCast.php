<?php

namespace Tsarturi\SimpleDTO\Castings;

use Throwable;
use Illuminate\Database\Eloquent\Model;
use Tsarturi\SimpleDTO\Exceptions\CastException;
use Tsarturi\SimpleDTO\Exceptions\CastTargetException;

class ModelCast implements Castable
{
    public function __construct(private string $modelClass)
    {
    }

    /**
     * @throws CastException|CastTargetException
     */
    public function cast(string $property, mixed $value): Model
    {
        if (is_string($value)) {
            $value = json_decode($value, true);
        }

        if (! is_array($value)) {
            throw new CastException($property);
        }

        try {
            $model = new $this->modelClass($value);
        } catch (Throwable) {
            throw new CastTargetException($property);
        }

        if (! ($model instanceof Model)) {
            throw new CastTargetException($property);
        }

        return $model;
    }
}
