<?php
namespace Tsarturi\SimpleDTO;

use Illuminate\Http\Request;
use PHPUnit\Util\InvalidJsonException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;
use Tsarturi\SimpleDto\Castings\Castable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Tsarturi\SimpleDto\Exceptions\CastTargetException;

abstract class DTO
{

    private FormRequest|null $formRequest = null;
    // private \Illuminate\Contracts\Validation\Validator|\Illuminate\Validation\Validator $validator;

    private array $dataOriginal = [];
    private array $dataValidated = [];
    public array $data = [];

    public array $rules = [];

    /**
     * Constructor of SimpleDTO
     *
     * @param array|null $data
     * @param string|null $formRequestClass
     *
     * @throws AuthorizationException|ValidationException|MissingCastTypeException|CastTargetException
     */
    public function __construct(?array $data = null, ?string $formRequestClass = null)
    {
        if (is_null($data)) {
            return;
        }

        $this->dataOriginal = $data;

        if ($formRequestClass) {

            $this->processFormRequest($formRequestClass, $data);

        } else {

            $this->processDataValidation($data);

        }

        $this->prepareData();
        $this->processDefaultValues();
        $this->processCasts();

        $this->fillProperties();

    }

    private function processCasts() {

        $casts = $this->casts();

        foreach ($casts as $key => $value) {

            if (! ($value instanceof Castable)) {
                throw new CastTargetException($key);
            }

            if (isset($this->data[$key])) {
                $this->data[$key] = $value->cast($key, $this->data[$key]);
            }

        }

    }

    private function processDefaultValues()
    {
        $defaults = $this->defaults();

        foreach ($defaults as $key => $value) {

            if (!property_exists($this, $key) || empty($this->{$key}) ) {
                $this->{$key} = $value;
                $this->data[$key] = $value;
                $this->dataValidated[$key] = $value;
            }

        }

    }

    protected function processDataValidation(?array $data = null) : void
    {

       $dataValidated = \Illuminate\Support\Facades\Validator::make(
            $this->data,
            $this->rules(),
            $this->messages(),
            $this->attributes()
        )->validate();

        $this->dataValidated = $dataValidated;
        $this->rules = $this->rules();

    }

    protected function processFormRequest(string $formRequestClass, array $data) : void
    {
        $this->formRequest = $formRequestClass::runFormRequest($data);
        $this->dataValidated = $this->formRequest->all();
        $this->rules = $this->formRequest->rules();

        if (!$this->formRequest->authorize()) {
            throw new AuthorizationException;
        }

    }

    protected function prepareData() : void
    {
        $this->data = $this->dataValidated;

        foreach ($this->dataOriginal as $key => $value) {
            if (!array_key_exists($key, $this->data)) {
                $this->data[$key] = $value;
            }
        }
    }

    protected function fillProperties(): void
    {
        foreach ($this->data as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public static function fromJson(string $json): self
    {
        $jsonDecoded = json_decode($json, true);
        if (!is_array($jsonDecoded)) {
            throw new InvalidJsonException();
        }

        return new static($jsonDecoded);
    }

    public static function fromArray(array $array): self
    {
        return new static($array);
    }

    public static function fromRequest(Request $request): self
    {
        return new static($request->all());
    }

    public static function fromModel(Model $model): self
    {
        return new static($model->toArray());
    }

    /**
     * Defines the validation rules. It's used with formRequest is null.
     */
    abstract protected function rules(): array;

    /**
     * Defines the custom messages for validator.
     */
    abstract protected function messages(): array;

    /**
     * Defines the custom attributes for validator.
     */
    abstract protected function attributes(): array;

    /**
     * Defines the default values for the properties.
     */
    abstract protected function defaults(): array;

    /**
     * Defines the type casting for the properties.
     */
    abstract protected function casts(): array;

}

