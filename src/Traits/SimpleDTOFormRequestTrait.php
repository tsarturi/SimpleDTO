<?php
namespace Tsarturi\SimpleDTO\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

trait SimpleDTOFormRequestTrait
{

    public static function getSimpleDTOFormRequest($value)
    {
        $value = self::getSimpleDTORequestByParameters($value);

        $validator = self::createFrom($value, new self());

        $validator->setContainer(app());

        $validator->prepareForValidation();

        $validator->setValidator(Validator::make($validator->all(), $validator->rules()));

        $instance = $validator->getValidatorInstance();

        if ($instance->fails()) {

            // return $instance->errors();

            $errors = $instance->errors();

            throw \Illuminate\Validation\ValidationException::withMessages( $errors->messages() );

        }

        $validator->passedValidation();

        return $validator;
    }


    protected static function getSimpleDTORequestByParameters($parameters) {
        $mockRequest = Request::create('', 'GET');
        $mockRequest->merge($parameters);
        return $mockRequest;
    }

}

