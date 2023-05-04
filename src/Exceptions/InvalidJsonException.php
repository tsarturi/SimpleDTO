<?php

namespace Tsarturi\SimpleDTO\Exceptions;

use Exception;
use Symfony\Component\HttpFoundation\Response;

class InvalidJsonException extends Exception
{
    public function __construct()
    {
        parent::__construct('The JSON string provided is not valid', Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
